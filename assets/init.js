// class PluginWrapper extends Header{
//
// }

(function (Drupal, drupalSettings ) {
  Drupal.behaviors.editorJS = {
    attach: function (context, settings) {
      context = context || document;
      settings = settings || drupalSettings;

      let data_element = context.querySelector('[name="field_content[0][hide_element]"]');
      
      if (data_element === null) {
        return;
      }
      
      let data = data_element.value;
      let newHeader = this.getPlugin(Header);
      const editor = new EditorJS({
        holder: 'editorjs',
        autofocus: true,
        tools: {
          header: newHeader
        },
        data: {
          blocks: JSON.parse(data)
        },
        onChange: () => {
          editor.save().then((savedData) => {
            data_element.value = JSON.stringify(savedData.blocks)
          });
        }
      });
      console.log(editor)
    },
    getPlugin: function (plugin) {
      let plugin_wrapper = plugin;
      plugin_wrapper.prototype.save = function(toolsContent) {
        return {
          text: toolsContent.innerHTML,
          level: this.currentLevel.number,
          pid: this.data.pid || 'new',
          type: this.data.type
        };
      };
      return plugin_wrapper;
    }
  }
}(Drupal, drupalSettings));