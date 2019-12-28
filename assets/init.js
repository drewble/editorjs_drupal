(function (Drupal) {
  'use strict';

  Drupal = Drupal && Drupal.hasOwnProperty('default') ? Drupal['default'] : Drupal;

  var manager = {
    tools: {},
    instances: {},
    triggerCallback: null,
    createHolder: function createHolder(element) {
      element.style.display = 'none';
      var holder = document.createElement('div');
      holder.id = 'holder-' + element.id;
      element.parentNode.appendChild(holder);
    },
    init: function init(holder_id, source) {
      var _this = this;
      this.instances[holder_id] = new EditorJS({
        holder: 'holder-' + holder_id,
        autofocus: true,
        tools: this.getTools(),
        data: {
          blocks: this.getBlocks(source)
        },
        onReady: function onReady() {
          _this.triggerCallback();
        }
      });
    },
    getBlocks: function getBlocks(source) {
      try {
        return JSON.parse(source);
      } catch (e) {
        return [{
          "type": "paragraph",
          "data": {
            "text": source
          }
        }];
      }
    },
    getTools: function getTools() {
      for (var ind in this.tools) {
        this.tools[ind].class = typeof this.tools[ind].class === 'string' ? window[this.tools[ind].class] : this.tools[ind].class;
      }
      return this.tools;
    }
  };

  var plugins = {
    count: 0,
    attachMultiple: function attachMultiple(paths, callback) {
      var _this = this;
      this.count = paths.length;
      for (var i = 0; i < paths.length; i++) {
        this.attach("/" + paths[i], function () {
          if (_this.count > 1) {
            _this.count--;
          } else {
            callback();
          }
        });
      }
    },
    attach: function attach(path, callback) {
      var ns = document.createElement("script");
      ns.onerror = this.onError;
      if (callback) {
        ns.onload = callback;
      }
      document.head.appendChild(ns);
      ns.src = path;
    },
    onError: function onError(e) {
      throw new URIError("The script " + e.target.src + " didn't load correctly.");
    }
  };

  Drupal.editors.editorjs = {
    attach: function attach(element, format) {
      manager.tools = format.editorSettings.tools;
      manager.createHolder(element);
      plugins.attachMultiple(format.editorSettings.plugins, function () {
        manager.init(element.id, element.value);
      });
    },
    detach: function detach(element, format, trigger) {
      var editor = manager.instances[element.id];
      if (editor) {
        editor.save().then(function (data) {
          element.value = JSON.stringify(data.blocks);
        });
        if (trigger !== 'serialize') {
          element.style.display = 'block';
          editor.destroy();
          delete manager.instances[element.id];
        }
      }
    },
    onChange: function onChange(element, callback) {
      manager.triggerCallback = callback;
    }
  };

}(Drupal));
