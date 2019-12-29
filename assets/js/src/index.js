import Drupal from 'Drupal'
import manager from './manager'
import plugins from './plugins'

Drupal.editors.editorjs = {
  attach: function (element, format) {
    manager.tools = format.editorSettings.tools;
    // Create holder for EditorJs.
    manager.createHolder(element);
    // Attach all plugins and initialize editorJs instance.
    plugins.attachMultiple(format.editorSettings.plugins, () => {
      manager.init(element.id, element.value)
    })
  },
  detach: function (element, format, trigger) {
    let editor = manager.instances[element.id];
    if (editor) {
      editor.save().then((data) => {
        element.value = JSON.stringify(data.blocks);
      });
      if (trigger !== 'serialize') {
        element.style.display = 'block';
        editor.destroy();
        delete manager.instances[element.id];
      }
    }
  },
  onChange: function (element, callback) {
    manager.triggerCallback = callback;
  }
};
