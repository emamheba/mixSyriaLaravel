
{{-- resources/views/membership/backend/type/type-js.blade.php --}}
<script>
  /**
   * Handles search and pagination via AJAX so we can update the table
   * without a full‑page load.
   */
  (function () {
      'use strict';
      const searchForm = document.getElementById('searchForm');
      const searchInput = document.getElementById('string_search');
      const tableWrapper = document.getElementById('typesTable');

      /** Fetch table HTML from the server and replace the wrapper content */
      const fetchTable = async (url) => {
          try {
              const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
              const html = await res.text();
              tableWrapper.innerHTML = html;
              attachPaginationListeners();
          } catch (err) {
              console.error(err);
          }
      };

      /** Attach click listeners to pagination links so they load via AJAX */
      const attachPaginationListeners = () => {
          tableWrapper.querySelectorAll('.pagination a').forEach(link => {
              link.addEventListener('click', e => {
                  e.preventDefault();
                  const url = new URL(e.currentTarget.href);
                  if (searchInput.value) {
                      url.searchParams.set('string_search', searchInput.value);
                  }
                  fetchTable(url.toString());
              });
          });
      };

      // Initial pagination link listeners
      attachPaginationListeners();

      // Search
      searchForm.addEventListener('submit', e => {
          e.preventDefault();
          const url = new URL(searchForm.action);
          url.searchParams.set('string_search', searchInput.value);
          fetchTable(url.toString());
      });
  })();
</script>
