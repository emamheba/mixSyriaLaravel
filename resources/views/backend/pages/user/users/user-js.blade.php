<script>
  document.addEventListener('DOMContentLoaded', function() {
    // ----- 1. Cascading country/state/city for both Add & Edit -----
    document.body.addEventListener('change', function(e) {
      const load = (url, selectEl, placeholder) => {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        if (!e.target.value) return;
        fetch(url + '?' + new URLSearchParams({ [e.target.name]: e.target.value }))
          .then(r => r.json())
          .then(arr => arr.forEach(i => {
            const opt = document.createElement('option');
            opt.value = i.id;
            opt.textContent = i[e.target.name === 'country_id' ? 'state' : 'city'];
            selectEl.append(opt);
          }))
          .catch(console.error);
      };

      // edit modals: ids contain edit_country / edit_state
      if (e.target.id.includes('edit_country')) {
        const state    = e.target.closest('.modal').querySelector('[id$="edit_state"]');
        const city     = e.target.closest('.modal').querySelector('[id$="edit_city"]');
        if (state && city) {
          city.innerHTML = '<option value="">Select City</option>';
          load(
            `{{ route('get.states') }}`, 
            state, 
            'Select State'
          );
        }
      }
      if (e.target.id.includes('edit_state')) {
        const city = e.target.closest('.modal').querySelector('[id$="edit_city"]');
        if (city) load(`{{ route('get.cities') }}`, city, 'Select City');
      }

      // add user form: country_id & state_id
      if (e.target.id === 'country_id') {
        const state = document.getElementById('state_id');
        const city  = document.getElementById('city_id');
        if (state && city) {
          city.innerHTML = '<option value="">Select City</option>';
          load(`{{ route('get.states') }}`, state, 'Select State');
        }
      }
      if (e.target.id === 'state_id') {
        const city = document.getElementById('city_id');
        if (city) load(`{{ route('get.cities') }}`, city, 'Select City');
      }
    });

    // ----- 2. Dynamic Search (debounced) -----
    const searchInput = document.getElementById('userSearch');
    const filterForm  = document.getElementById('userFilterForm');
    let debounceTimer;
    if (searchInput && filterForm) {
      searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => filterForm.submit(), 500);
      });
    }

    // ----- 3. Reset Button -----
    const resetBtn = document.getElementById('resetSearch');
    if (resetBtn && filterForm) {
      resetBtn.addEventListener('click', () => {
        filterForm.reset();
        filterForm.querySelectorAll('select').forEach(sel => {
          sel.value = '';
          sel.dispatchEvent(new Event('change'));
        });
        filterForm.submit();
      });
    }
  });
</script>
