// 'use strict';

// (function () {
//   // Init custom option check
//   window.Helpers.initCustomOptionCheck();

//   const flatpickrRange = document.querySelector('.flatpickr'),
//     phoneMask = document.querySelector('.contact-number-mask'),
//     plCountry = $('#plCountry'),
//     plFurnishingDetailsSuggestionEl = document.querySelector('#plFurnishingDetails');

//   // Phone Number Input Mask
//   if (phoneMask) {
//     new Cleave(phoneMask, {
//       phone: true,
//       phoneRegionCode: 'US'
//     });
//   }

//   // select2 (Country)

//   if (plCountry) {
//     plCountry.wrap('<div class="position-relative"></div>');
//     plCountry.select2({
//       placeholder: 'Select country',
//       dropdownParent: plCountry.parent()
//     });
//   }

//   if (flatpickrRange) {
//     flatpickrRange.flatpickr();
//   }

//   // Tagify (Furnishing details)
//   const furnishingList = [
//     'Fridge',
//     'TV',
//     'AC',
//     'WiFi',
//     'RO',
//     'Washing Machine',
//     'Sofa',
//     'Bed',
//     'Dining Table',
//     'Microwave',
//     'Cupboard'
//   ];
//   if (plFurnishingDetailsSuggestionEl) {
//     const plFurnishingDetailsSuggestion = new Tagify(plFurnishingDetailsSuggestionEl, {
//       whitelist: furnishingList,
//       maxTags: 10,
//       dropdown: {
//         maxItems: 20,
//         classname: 'tags-inline',
//         enabled: 0,
//         closeOnSelect: false
//       }
//     });
//   }

//   // Vertical Wizard
//   // --------------------------------------------------------------------

//   const wizardPropertyListing = document.querySelector('#wizard-property-listing');
//   if (typeof wizardPropertyListing !== undefined && wizardPropertyListing !== null) {
//     // Wizard form
//     const wizardPropertyListingForm = wizardPropertyListing.querySelector('#wizard-property-listing-form');
//     // Wizard steps
//     const wizardPropertyListingFormStep1 = wizardPropertyListingForm.querySelector('#category-details');
//     const wizardPropertyListingFormStep2 = wizardPropertyListingForm.querySelector('#listing-details');
//     const wizardPropertyListingFormStep3 = wizardPropertyListingForm.querySelector('#images-videos');
//     const wizardPropertyListingFormStep4 = wizardPropertyListingForm.querySelector('#location-details');
//     const wizardPropertyListingFormStep5 = wizardPropertyListingForm.querySelector('#contact-details');
//     // Wizard next prev button
//     const wizardPropertyListingNext = [].slice.call(wizardPropertyListingForm.querySelectorAll('.btn-next'));
//     const wizardPropertyListingPrev = [].slice.call(wizardPropertyListingForm.querySelectorAll('.btn-prev'));

//     const validationStepper = new Stepper(wizardPropertyListing, {
//       linear: true
//     });

//     // Category Details
//     const FormValidation1 = FormValidation.formValidation(wizardPropertyListingFormStep1, {
//       fields: {
//         // category_id: {
//         //   validators: {
//         //     notEmpty: {
//         //       message: 'Please select a category'
//         //     }
//         //   }
//         // },
//         // sub_category_id: {
//         //   validators: {
//         //     notEmpty: {
//         //       message: 'Please select a sub-category'
//         //     }
//         //   }
//         // },
//         // child_category_id: {
//         //   validators: {
//         //     notEmpty: {
//         //       message: 'Please select a child category'
//         //     }
//         //   }
//         // }
//       },

//       plugins: {
//         trigger: new FormValidation.plugins.Trigger(),
//         bootstrap5: new FormValidation.plugins.Bootstrap5({
//           // Use this for enabling/changing valid/invalid class
//           eleValidClass: '',
//           rowSelector: '.col-sm-6'
//         }),
//         autoFocus: new FormValidation.plugins.AutoFocus(),
//         submitButton: new FormValidation.plugins.SubmitButton()
//       },
//       init: instance => {
//         instance.on('plugins.message.placed', function (e) {
//           if (e.element.parentElement.classList.contains('input-group')) {
//             e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
//           }
//         });
//       }
//     }).on('core.form.valid', function () {
//       validationStepper.next();
//     });

//     // Listing Details
//     const FormValidation2 = FormValidation.formValidation(wizardPropertyListingFormStep2, {
//       fields: {
//         title: {
//           validators: {
//             notEmpty: {
//               message: 'Please enter a title'
//             }
//           }
//         },
//         price: {
//           validators: {
//             notEmpty: {
//               message: 'Please enter a price'
//             },
//             numeric: {
//               message: 'The price must be a numeric value'
//             }
//           }
//         },
//         description: {
//           validators: {
//             notEmpty: {
//               message: 'Please enter a description'
//             }
//           }
//         }
//       },

//       plugins: {
//         trigger: new FormValidation.plugins.Trigger(),
//         bootstrap5: new FormValidation.plugins.Bootstrap5({
//           eleValidClass: '',
//           rowSelector: '.col-sm-6'
//         }),
//         autoFocus: new FormValidation.plugins.AutoFocus(),
//         submitButton: new FormValidation.plugins.SubmitButton()
//       }
//     }).on('core.form.valid', function () {
//       validationStepper.next();
//     });

//     // Images & Videos
//     const FormValidation3 = FormValidation.formValidation(wizardPropertyListingFormStep3, {
//       fields: {
//         image: {
//           validators: {
//             notEmpty: {
//               message: 'Please upload an image'
//             }
//           }
//         }
//       },

//       plugins: {
//         trigger: new FormValidation.plugins.Trigger(),
//         bootstrap5: new FormValidation.plugins.Bootstrap5({
//           eleValidClass: '',
//           rowSelector: '.col-sm-6'
//         }),
//         autoFocus: new FormValidation.plugins.AutoFocus(),
//         submitButton: new FormValidation.plugins.SubmitButton()
//       }
//     }).on('core.form.valid', function () {
//       validationStepper.next();
//     });

//     // Location Details
//     const FormValidation4 = FormValidation.formValidation(wizardPropertyListingFormStep4, {
//       fields: {
//         address: {
//           validators: {
//             notEmpty: {
//               message: 'Please enter an address'
//             }
//           }
//         }
//       },

//       plugins: {
//         trigger: new FormValidation

// .plugins.Trigger(),
//         bootstrap5: new FormValidation.plugins.Bootstrap5({
//           eleValidClass: '',
//           rowSelector: '.col-sm-6'
//         }),
//         autoFocus: new FormValidation.plugins.AutoFocus(),
//         submitButton: new FormValidation.plugins.SubmitButton()
//       }
//     }).on('core.form.valid', function () {
//       validationStepper.next();
//     });

//     // Contact Details
//     const FormValidation5 = FormValidation.formValidation(wizardPropertyListingFormStep5, {
//       fields: {
//         phone: {
//           validators: {
//             notEmpty: {
//               message: 'Please enter a phone number'
//             }
//           }
//         }
//       },

//       plugins: {
//         trigger: new FormValidation.plugins.Trigger(),
//         bootstrap5: new FormValidation.plugins.Bootstrap5({
//           eleValidClass: '',
//           rowSelector: '.col-sm-6'
//         }),
//         autoFocus: new FormValidation.plugins.AutoFocus(),
//         submitButton: new FormValidation.plugins.SubmitButton()
//       }
//     }).on('core.form.valid', function () {
//       alert('Listing Submitted!');
//     });

//     wizardPropertyListingNext.forEach(item => {
//       item.addEventListener('click', event => {
//         switch (validationStepper._currentIndex) {
//           case 0:
//             FormValidation1.validate();
//             break;
//           case 1:
//             FormValidation2.validate();
//             break;
//           case 2:
//             FormValidation3.validate();
//             break;
//           case 3:
//             FormValidation4.validate();
//             break;
//           case 4:
//             FormValidation5.validate();
//             break;
//           default:
//             break;
//         }
//       });
//     });

//     wizardPropertyListingPrev.forEach(item => {
//       item.addEventListener('click', event => {
//         validationStepper.previous();
//       });
//     });
//   }
// })();




(function () {
    // Init custom option check
    window.Helpers.initCustomOptionCheck();

    const flatpickrRange = document.querySelector('.flatpickr'),
      phoneMask = document.querySelector('.contact-number-mask'),
      plCountry = $('#plCountry'),
      plFurnishingDetailsSuggestionEl = document.querySelector('#plFurnishingDetails');

    // Phone Number Input Mask
    if (phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'US'
      });
    }

    // select2 (Country)
    if (plCountry) {
      plCountry.wrap('<div class="position-relative"></div>');
      plCountry.select2({
        placeholder: 'اختر الدولة',
        dropdownParent: plCountry.parent()
      });
    }

    if (flatpickrRange) {
      flatpickrRange.flatpickr();
    }

    // Tagify (Furnishing details)
    const furnishingList = [
      'Fridge', 'TV', 'AC', 'WiFi', 'Washing Machine', 'Sofa', 'Bed', 'Dining Table', 'Microwave', 'Cupboard'
    ];
    if (plFurnishingDetailsSuggestionEl) {
      const plFurnishingDetailsSuggestion = new Tagify(plFurnishingDetailsSuggestionEl, {
        whitelist: furnishingList,
        maxTags: 10,
        dropdown: {
          maxItems: 20,
          classname: 'tags-inline',
          enabled: 0,
          closeOnSelect: false
        }
      });
    }

    // Vertical Wizard
    const wizardPropertyListing = document.querySelector('#wizard-property-listing');
    if (typeof wizardPropertyListing !== undefined && wizardPropertyListing !== null) {
      // Wizard form
      const wizardPropertyListingForm = wizardPropertyListing.querySelector('#wizard-property-listing-form');
      // Wizard steps
      const wizardPropertyListingFormStep1 = wizardPropertyListingForm.querySelector('#personal-details');
      const wizardPropertyListingFormStep2 = wizardPropertyListingForm.querySelector('#property-details');
      const wizardPropertyListingFormStep3 = wizardPropertyListingForm.querySelector('#property-features');
      const wizardPropertyListingFormStep4 = wizardPropertyListingForm.querySelector('#property-area');
      const wizardPropertyListingFormStep5 = wizardPropertyListingForm.querySelector('#price-details');
      // Wizard next prev button
      const wizardPropertyListingNext = [].slice.call(wizardPropertyListingForm.querySelectorAll('.btn-next'));
      const wizardPropertyListingPrev = [].slice.call(wizardPropertyListingForm.querySelectorAll('.btn-prev'));

      const validationStepper = new Stepper(wizardPropertyListing, {
        linear: true
      });

      // Personal Details
      const FormValidation1 = FormValidation.formValidation(wizardPropertyListingFormStep1, {
        fields: {
          plFirstName: {
            validators: {
              notEmpty: {
                message: 'الرجاء إدخال الاسم الأول'
              }
            }
          },
          plLastName: {
            validators: {
              notEmpty: {
                message: 'الرجاء إدخال الاسم الأخير'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleInvalidClass: '',
            eleValidClass: '',
            rowSelector: '.col-sm-6'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        },
        init: instance => {
          instance.on('plugins.message.placed', function (e) {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
        }
      }).on('core.form.valid', function () {
        validationStepper.next();
      });

      // Property Details
      const FormValidation2 = FormValidation.formValidation(wizardPropertyListingFormStep2, {
        fields: {
          plCategory: {
            validators: {
              notEmpty: {
                message: 'الرجاء اختيار الفئة'
              }
            }
          },
          plTitle: {
            validators: {
              notEmpty: {
                message: 'الرجاء إدخال العنوان'
              },
              stringLength: {
                max: 191,
                message: 'يجب ألا يتجاوز العنوان 191 حرفًا'
              }
            }
          },
          plDescription: {
            validators: {
              notEmpty: {
                message: 'الرجاء إدخال الوصف'
              },
              stringLength: {
                min: 150,
                message: 'يجب أن يكون الوصف 150 حرفًا على الأقل'
              }
            }
          },
          plPrice: {
            validators: {
              notEmpty: {
                message: 'الرجاء إدخال السعر'
              },
              numeric: {
                message: 'يجب أن يكون السعر قيمة رقمية'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleInvalidClass: '',
            eleValidClass: '',
            rowSelector: '.col-sm-6'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        validationStepper.next();
      });

      // Property Features
      const FormValidation3 = FormValidation.formValidation(wizardPropertyListingFormStep3, {
        fields: {
          // Add validation for attributes if needed
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleInvalidClass: '',
            eleValidClass: '',
            rowSelector: '.col-sm-6'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        validationStepper.next();
      });

      // Property Area
      const FormValidation4 = FormValidation.formValidation(wizardPropertyListingFormStep4, {
        fields: {
          // Add validation for video URL, tags, images, etc.
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleInvalidClass: '',
            eleValidClass: '',
            rowSelector: '.col-md-12'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        validationStepper.next();
      });

      // Price Details
      const FormValidation5 = FormValidation.formValidation(wizardPropertyListingFormStep5, {
        fields: {
          // Add validation for price details if needed
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleInvalidClass: '',
            eleValidClass: '',
            rowSelector: '.col-md-12'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        alert('تم الإرسال..!!');
      });

      wizardPropertyListingNext.forEach(item => {
        item.addEventListener('click', event => {
          switch (validationStepper._currentIndex) {
            case 0:
              FormValidation1.validate();
              break;
            case 1:
              FormValidation2.validate();
              break;
            case 2:
              FormValidation3.validate();
              break;
            case 3:
              FormValidation4.validate();
              break;
            case 4:
              FormValidation5.validate();
              break;
            default:
              break;
          }
        });
      });

      wizardPropertyListingPrev.forEach(item => {
        item.addEventListener('click', event => {
          switch (validationStepper._currentIndex) {
            case 4:
              validationStepper.previous();
              break;
            case 3:
              validationStepper.previous();
              break;
            case 2:
              validationStepper.previous();
              break;
            case 1:
              validationStepper.previous();
              break;
            case 0:
            default:
              break;
          }
        });
      });
    }
  })();
