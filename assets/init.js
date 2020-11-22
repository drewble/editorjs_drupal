(function (D, Editor) {
  D.editorJs = {
    tools: {
      header: Header,
      list: {
        class: List,
        inlineToolbar: true,
      },
      checklist: {
        class: Checklist,
        inlineToolbar: true,
      },
      linkTool: {
        class: LinkTool,
        inlineToolbar: false,
        config: {
          endpoint: '/admin/editorjs/link',
        }
      },
      table: {
        class: Table,
        inlineToolbar: true,
      },
      code: {
        class: CodeTool
      },
      delimiter: {
        class: Delimiter
      },
      image: {
        class: ImageTool,
        config: {
          endpoints: {
            byFile: '/admin/editorjs/upload',
            byUrl: '/admin/editorjs/fetch',
          }
        }
      }
    }
  }
  D.behaviors.editorJs = {
    attach: function (context) {
      context = context || document;
      let items = context.querySelectorAll('.editorjs');
      items.forEach(item => {
        let holder = document.createElement('div');
        holder.classList.add('editorjs_holder');
        item.parentNode.insertBefore(holder, item.nextSibling);
        let ei = new Editor({
          holder: holder,
          logLevel: 'WARN',
          tools:  D.editorJs.tools,
          data: {
            blocks: JSON.parse(item.value)
          },
          onChange: function () {
            ei.save().then((data) => {
              item.value = JSON.stringify(data.blocks)
            }).catch((error) => {
              console.log('Saving failed: ', error)
            });
          }
        });

      })
    }
  }
}(Drupal, EditorJS))
