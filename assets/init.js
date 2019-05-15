// class PluginWrapper extends Header{
//
// }

(function (Drupal, drupalSettings) {
  Drupal.behaviors.editorJS = {
    attach: function (context, settings) {
      context = context || document;
      settings = settings || drupalSettings;

      let data_element = context.querySelector('[name="field_content[0][hide_element]"]');

      if (data_element === null) {
        return;
      }

      let data = data_element.value;
      let newHeader = this.getPlugin(Header, 'header');
      let newPagaraph = this.getPlugin(Paragraph, 'paragraph_default');
      const editor = new EditorJS({
        holder: 'editorjs',
        autofocus: true,
        tools: {
          header: {
            class: newHeader,
            config: {
              placeholder: Drupal.t('Enter a header')
            }
          },
          paragraph_default: {
            class: newPagaraph,
            inlineToolbar: true,
          },
          list: {
            class: this.getPlugin(List, 'list'),
            inlineToolbar: true,
          },
        },
        initialBlock: 'paragraph_default',
        data: {
          blocks: JSON.parse(data)
        },
        onChange: () => {
          editor.save().then((savedData) => {
            data_element.value = JSON.stringify(savedData.blocks)
          });
        }
      });
    },
    getPlugin: function (plugin, plugin_type) {
      let plugin_wrapper = plugin;
      plugin_wrapper.prototype.save = function (toolsContent) {
        let output = {
          pid: this.data.pid || 'new'
        };
        if (plugin_type === 'header') {
          output.text = toolsContent.innerHTML;
          output.level = this.currentLevel.number;
        }

        if (plugin_type === 'paragraph_default') {
          output.text = toolsContent.innerHTML;
        }

        if (plugin_type === 'list') {
          output.style = this.data.style;
          output.items = this.data.items;
        }

        return output;
      };

      return plugin_wrapper;
    }
  }
}(Drupal, drupalSettings));