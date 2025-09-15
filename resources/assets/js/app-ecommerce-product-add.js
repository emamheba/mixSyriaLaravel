/**
 * App eCommerce Add Product Script
 */
'use strict';

// Javascript to handle the e-commerce product add page

(function () {
  
  // Comment editor
  const commentEditor = document.querySelector('.comment-editor');

  if (commentEditor) {
    new Quill(commentEditor, {
      modules: {
        toolbar: '.comment-toolbar'
      },
      placeholder: 'Product Description',
      theme: 'snow'
    });
  }

  // previewTemplate: Updated Dropzone default previewTemplate
  const previewTemplate = `<div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
        </div>
      </div>
      <div class="dz-filename" data-dz-name></div>
      <div class="dz-size" data-dz-size></div>
    </div>
  </div>`;

  const dropzoneMulti = document.querySelector('#dropzone-basic');
  if (dropzoneMulti) {
      
      const myDropzone = new Dropzone(dropzoneMulti, {
          url: '#',
          previewTemplate: previewTemplate,
          clickable: true,
          addRemoveLinks: true,
          maxFiles: 5,
          maxFilesize: 5,
          acceptedFiles: 'image/*',
          autoProcessQueue: false,
          parallelUploads: 5,
          init: function() {
              const hiddenInput = document.querySelector('#hidden-images');
              
              this.on("addedfile", file => {
                  const dataTransfer = new DataTransfer();
                  Array.from(hiddenInput.files).forEach(f => dataTransfer.items.add(f));
                  dataTransfer.items.add(file);
                  hiddenInput.files = dataTransfer.files;
    
                  const reader = new FileReader();
                  reader.onload = (e) => {
                      file.previewElement.querySelector('img').src = e.target.result;
                  };
                  reader.readAsDataURL(file);
              });

              this.on("removedfile", file => {
                  const dataTransfer = new DataTransfer();
                  Array.from(hiddenInput.files)
                      .filter(f => f.name !== file.name)
                      .forEach(f => dataTransfer.items.add(f));
                  hiddenInput.files = dataTransfer.files;
              });

              dropzoneMulti.querySelector('.btn').addEventListener('click', () => {
                  this.hiddenFileInput.click();
              });
          }
      });
  }
  // Basic Tags
  const tagifyBasicEl = document.querySelector('#ecommerce-product-tags');
  if (tagifyBasicEl) {
    const TagifyBasic = new Tagify(tagifyBasicEl);
  }

  // Flatpickr
  const productDate = document.querySelector('.product-date');
  if (productDate) {
    productDate.flatpickr({
      monthSelectorType: 'static',
      defaultDate: new Date()
    });
  }
})();

// Jquery to handle the e-commerce product add page
$(function () {
  // Select2
  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: $this.parent(),
        placeholder: $this.data('placeholder') // for dynamic placeholder
      });
    });
  }

  var formRepeater = $('.form-repeater');

  // Form Repeater
  if (formRepeater.length) {
    var row = 2;
    var col = 1;
    formRepeater.on('submit', function (e) {
      e.preventDefault();
    });
    formRepeater.repeater({
      show: function () {
        var fromControl = $(this).find('.form-control, .form-select');
        var formLabel = $(this).find('.form-label');

        fromControl.each(function (i) {
          var id = 'form-repeater-' + row + '-' + col;
          $(fromControl[i]).attr('id', id);
          $(formLabel[i]).attr('for', id);
          col++;
        });

        row++;
        $(this).slideDown();
        $('.select2-container').remove();
        $('.select2.form-select').select2({
          placeholder: 'Placeholder text'
        });
        $('.select2-container').css('width', '100%');
        $('.form-repeater:first .form-select').select2({
          dropdownParent: $(this).parent(),
          placeholder: 'Placeholder text'
        });
        $('.position-relative .select2').each(function () {
          $(this).select2({
            dropdownParent: $(this).closest('.position-relative')
          });
        });
      }
    });
  }
});