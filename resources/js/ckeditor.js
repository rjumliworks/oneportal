import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor.js';

// Core plugins
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials.js';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph.js';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold.js';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic.js';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline.js';
import List from '@ckeditor/ckeditor5-list/src/list.js';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment.js';

// Build editor
class ClassicEditor extends ClassicEditorBase {}

// Register plugins
ClassicEditor.builtinPlugins = [
  Essentials,
  Paragraph,
  Bold,
  Italic,
  Underline,
  List,
  Alignment
];

// Toolbar configuration
ClassicEditor.defaultConfig = {
  toolbar: {
    items: [
      'bold',
      'italic',
      'underline',
      '|',
      'alignment:left',
      'alignment:center',
      'alignment:right',
      'alignment:justify',
      '|',
      'bulletedList',
      'numberedList',
      '|',
      'undo',
      'redo'
    ]
  },
  alignment: {
    options: ['left', 'center', 'right', 'justify']
  },
  language: 'en'
};

export default ClassicEditor;
