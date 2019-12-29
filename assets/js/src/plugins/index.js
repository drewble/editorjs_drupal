/**
 * Provider object for EditorJs tools attach.
 */
export default {

  /**
   * This count tools for attach.
   */
  count: 0,

  /**
   * Attach Multiple tools and call function after attach
   *
   * @param {string[]} paths
   * @param {function} callback
   */
  attachMultiple: function (paths, callback) {
    this.count = paths.length;
    for (let i = 0; i < paths.length; i++) {
      this.attach(`/${paths[i]}`, () => {
        // Call function after include plugins.
        if (this.count > 1) {
          this.count--;
        } else {
          callback();
        }
      })
    }
  },

  /**
   * Attach tool and call function.
   *
   * @param {string} path
   * @param {function} callback
   */
  attach: function (path, callback) {
    var ns = document.createElement("script");
    ns.onerror = this.onError;
    if (callback) {
      ns.onload = callback;
    }
    document.head.appendChild(ns);
    ns.src = path;
  },

  /**
   * This method is called if an connect tools error.
   *
   * @param {event} e
   */
  onError: function (e) {
    throw new URIError("The script " + e.target.src + " didn't load correctly.");
  }

}
