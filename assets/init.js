(function (D, Editor) {

  D.behaviors.editorJs = {
    /**
     * The list containing callback functions for call after load script.
     */
    callbacks: [],
    /**
     * The list containing paths to script.
     */
    scripts: [],
    /**
     * The function attach all dependencies scripts.
     *
     * @param {object} settings
     *   The editor settings.
     * @param {Function} callback
     *   The callback function.
     */
    attachDependencies: function (settings, callback) {
      let scripts = [];
      Object.keys(settings).map(field => {
        Object.keys(settings[field].tools || {}).map(tool => {
          let file = settings[field].tools[tool].class_file || '';
          if (this.scripts.indexOf(file) < 0) {
            this.scripts.push(file)
            scripts.push(file)
          }
        })
      });

      let loaded_count = 0;
      scripts.forEach(path => {
        this.loadScript(path, () => {
          loaded_count++;
          if (scripts.length === loaded_count) {
            this.callbacks.forEach(item => {
              if (!item.use) {
                item.use = true;
                item['callback']();
              }
            })
          }
        })
      })
      this.callbacks.push({
        'callback': callback,
        'use': false,
      })
    },
    /**
     * The prepare tools for init editorJs.
     *
     * @param {object} tools
     *   The source tools list.
     *
     * @return {object}
     *   The tools after prepare.
     */
    prepareTools: function (tools) {
      Object.keys(tools).map(tool => {
        tools[tool].class = window[tools[tool].class]
      })
      return tools;
    },
    attach: function (context, settings) {
      context = context || document;
      settings = settings.editorjs || {};
      let items = context.querySelectorAll('.editorjs');
      items.forEach(item => {
        this.attachDependencies(settings, () => {

          let holder = document.createElement('div');
          holder.classList.add('editorjs_holder');
          item.parentNode.insertBefore(holder, item.nextSibling);
          let data = {};
          if (item.value) {
            data['blocks'] = JSON.parse(item.value);
          }
          let ei = new Editor({
            holder: holder,
            logLevel: 'WARN',
            tools:  this.prepareTools(settings[item.dataset.fieldName].tools || {}),
            data: data,
            onChange: function () {
              ei.save().then((data) => {
                item.value = JSON.stringify(data.blocks)
              }).catch((error) => {
                console.log('Saving failed: ', error)
              });
            }
          });
        })

      })
    },
    /**
     * Attach scripts.
     *
     * @param {string} url
     *   The path to script.
     * @param {Function} callback
     *   The callback function after attach script.
     */
    loadScript: function (url, callback) {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = url;
      script.onreadystatechange = callback;
      script.onload = callback;

      document.body.appendChild(script);
    }
  }
}(Drupal, EditorJS))
