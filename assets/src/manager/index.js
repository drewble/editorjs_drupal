/**
 * Defines a EditorJs manager.
 */
export default {

  /**
   * This tools settings.
   */
  tools: {},

  /**
   * This EditorJs instance collection.
   */
  instances: {},

  /**
   * Callback on change.
   *
   * @see /core/modules/editor/js/editor.es6.js:314
   */
  triggerCallback: null,

  /**
   * Create wrapper for EditorJs instance.
   *
   * @param {Element} element
   */
  createHolder: function (element) {
    element.style.display = 'none';
    let holder = document.createElement('div');
    holder.id = 'holder-' + element.id;
    element.parentNode.appendChild(holder);
  },

  /**
   * Create EditorJs instance.
   */
  init: function (holder_id, source) {
    this.instances[holder_id] = new EditorJS({
      holder: 'holder-' + holder_id,
      autofocus: true,
      tools: this.getTools(),
      data: {
        blocks: this.getBlocks(source)
      },
      onReady: () => {
        this.triggerCallback();
      }
    })
  },

  /**
   * Convert source value to editorJs data.
   *
   * @param {string} source
   * @returns {{data: {text: *}, type: string}[]|any}
   */
  getBlocks: function (source) {
    try {
      return JSON.parse(source);
    } catch (e) {
      return [
        {
          "type": "paragraph",
          "data": {
            "text": source,
          }
        },
      ];
    }
  },

  /**
   * Returns converted backend settings.
   *
   * @returns {{}}
   */
  getTools: function () {
    for (let ind in this.tools) {
      this.tools[ind].class = typeof this.tools[ind].class === 'string' ? window[this.tools[ind].class] : this.tools[ind].class
    }
    return this.tools;
  }

}
