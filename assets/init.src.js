
(function (Drupal, drupalSettings) {
  'use strict';
  Drupal.editors.editorjs = {
    count: 0,
    elements: {},
    onChangeCallback: null,
    attach: function (element, format) {
      this.createHolderElement(element);
      this.attachPlugins(format.editorSettings.plugins, element.id);
    },
    detach: function (element, format, trigger) {
      element = Drupal.editors.editorjs.elements[element.id] || element;
      let editor = Drupal.editorjs.instances[element.id];
      if (editor) {
        editor.save().then((data) => {
          element.value = JSON.stringify(data.blocks);
        });
        if (trigger !== 'serialize') {
          element.style.display = 'block';
          editor.destroy();
          delete Drupal.editorjs.instances[element.id];
        }
      }
    },
    onChange: function (element, callback) {
      // Callback after initialize EditorJs.
      this.onChangeCallback = callback;
    },
    attachPlugins: function (paths, element_id) {
      paths = JSON.parse(paths);
      for (let i = 0; i < paths.length; i++) {
        this.attachPlugin(`/${paths[i]}`, function () {
          Drupal.editors.editorjs.attachPluginOnload(element_id)
        })
      }
    },
    attachPlugin: function (url, callback) {
      var ns = document.createElement("script");
      ns.onerror = this.attachPluginOnerror;
      if (callback) {
        ns.onload = callback;
      }
      document.head.appendChild(ns);
      ns.src = url;
    },
    attachPluginOnload: function (element_id) {
      if (Drupal.editors.editorjs.count > 1) {
        Drupal.editors.editorjs.count--;
      } else {
        // Trigger onchange callback.
        Drupal.editors.editorjs.onChangeCallback();
        Drupal.editorjs.init(element_id, this.elements[element_id].value);
      }
    },
    attachPluginOnerror: function (e) {
      throw new URIError("The script " + e.target.src + " didn't load correctly.");
    },
    createHolderElement: function (element) {
      this.elements[element.id] = element;
      this.elements[element.id].style.display = 'none';
      let holder = document.createElement('div');
      holder.id = 'holder-' + element.id;
      element.parentNode.appendChild(holder);
    }
  };
  Drupal.editorjs = {
    instances: {},
    init: function (holder_id, source) {
      // console.log(source)
      this.instances[holder_id] = new EditorJS({
        holder: 'holder-' + holder_id,
        autofocus: true,
        tools: {
          header: {
            class: Header,
            inlineToolbar: true
          },
        },
        data: {
          blocks: [
            {
              "type": "paragraph",
              "data": {
                "text": source,
              }
            },
          ]
        },
        onChange: function () {
          Drupal.editorjs.instances[holder_id].save().then((data) => {
            Drupal.editors.editorjs.elements[holder_id].value = JSON.stringify(data.blocks);
          });
        }
      })
    },
    attach: function (context) {
      // context = context || document;
      // settings = settings || drupalSettings;
      //
      // let elems = settings.editorjs.elements || [];
      // let plugins = settings.editorjs.plugins || [];
      // Drupal.editorJS.attachPlugins(plugins);
      return;
      elems.forEach(elem => {
        let data_element = context.querySelector('[name="' + elem + '"]');
        if (data_element === null) {
          return;
        }
        let holder_id = 'for-' + elem;
        let holder_elem = document.createElement('div');
        holder_elem.setAttribute('id', holder_id);
        data_element.parentNode.insertBefore(holder_elem, data_element.nextSibling);

      })
      // let data_element = context.querySelector('[name="field_content[0][hide_element]"]');
      //

      //
      // let data = data_element.value;
      // let newHeader = this.getPlugin(Header, 'header');
      // let newPagaraph = this.getPlugin(Paragraph, 'paragraph_default');
      // const editor = new EditorJS({
      //   holder: 'editorjs',
      //   autofocus: true,
      //   tools: {
      //     header: {
      //       class: newHeader,
      //       config: {
      //         placeholder: Drupal.t('Enter a header')
      //       }
      //     },
      //     paragraph_default: {
      //       class: newPagaraph,
      //       inlineToolbar: true,
      //     },
      //     list: {
      //       class: this.getPlugin(List, 'list'),
      //       inlineToolbar: true,
      //     },
      //   },
      //   initialBlock: 'paragraph_default',
      //   data: {
      //     blocks: JSON.parse(data)
      //   },
      //   onChange: () => {
      //     editor.save().then((savedData) => {
      //       data_element.value = JSON.stringify(savedData.blocks)
      //     });
      //   }
      // });
    },
    // getPlugin: function (plugin, plugin_type) {
    //   let plugin_wrapper = plugin;
    //   plugin_wrapper.prototype.save = function (toolsContent) {
    //     let output = {
    //       pid: this.data.pid || 'new'
    //     };
    //     if (plugin_type === 'header') {
    //       output.text = toolsContent.innerHTML;
    //       output.level = this.currentLevel.number;
    //     }
    //
    //     if (plugin_type === 'paragraph_default') {
    //       output.text = toolsContent.innerHTML;
    //     }
    //
    //     if (plugin_type === 'list') {
    //       output.style = this.data.style;
    //       output.items = this.data.items;
    //     }
    //
    //     return output;
    //   };
    //
    //   return plugin_wrapper;
    // }
  }
}(Drupal, drupalSettings));
