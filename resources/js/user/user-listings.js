document.addEventListener('DOMContentLoaded', function() {
  // Delete Listing Handler
  document.querySelectorAll('.delete-listing').forEach(button => {
      button.addEventListener('click', function() {
          const listingId = this.dataset.listingId;
          const form = document.getElementById('deleteListingForm');
          form.action = `/user/listings/${listingId}`;
          
          new bootstrap.Modal(document.getElementById('deleteListingModal')).show();
      });
  });

  // Publication Status Toggle
  document.querySelectorAll('.swal_status_change').forEach(button => {
      button.addEventListener('click', function() {
          const listingId = this.dataset.listingId;
          const form = this.nextElementSibling; // form associated with the button
          
          Swal.fire({
              title: 'تغيير حالة النشر',
              text: 'هل أنت متأكد من تغيير حالة النشر؟',
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'نعم',
              cancelButtonText: 'إلغاء'
          }).then((result) => {
              if (result.isConfirmed) {
                  form.querySelector('button[type="submit"]').click(); // Trigger form submission
              }
          });
      });
  });

  // Publication Status Toggle with AJAX for `switch-input`
  document.querySelectorAll('.switch-input').forEach(button => {
      button.addEventListener('change', function() {
          const listingId = this.getAttribute('data-listing-id');
          const label = document.getElementById('publish-label-' + listingId);
          
          // Change label text between Published and Unpublished based on the checkbox state
          if (this.checked) {
              label.textContent = 'Published'; // Change the label text to "Published"
          } else {
              label.textContent = 'Unpublished'; // Change the label text to "Unpublished"
          }

          // Send the update using AJAX
          fetch(`/user/listing/published-on-off/${listingId}`, {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({
                  is_published: this.checked ? 1 : 0 // Set the publication status based on the checkbox state
              })
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  toastr.success(data.message);
              } else {
                  toastr.error(data.message);
                  // Revert back to the original state if there's an error
                  this.checked = !this.checked;
              }
          })
          .catch(error => {
              toastr.error("Something went wrong. Please try again.");
              this.checked = !this.checked; // Revert back to the original state
          });
      });
  });

  // Filter Handlers
  ['#filterStatus', '#filterCategory', '#filterPublished'].forEach(selector => {
      document.querySelector(selector).addEventListener('change', function() {
          const params = new URLSearchParams({
              status: document.getElementById('filterStatus').value,
              category: document.getElementById('filterCategory').value,
              published: document.getElementById('filterPublished').value
          });
          
          window.location.href = `${window.location.pathname}?${params}`;
      });
  });

  // Initialize Tooltips
  new bootstrap.Tooltip(document.body, {
      selector: '[data-bs-toggle="tooltip"]'
  });
});
