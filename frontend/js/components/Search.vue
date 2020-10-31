<template>
  <div class="d-flex align-content-center align-items-center py-4">
    <div class="logo mr-4">
      <img src="frontend/logo.png" alt="Mamarflix">
    </div>

    <div class="form-group flex-grow-1 mb-0">
      <input type="search"
             class="form-control shadow"
             ref="search"
             v-model="query"
             :placeholder="`Rechercher dans ${count} films (/)`">
    </div>
  </div>
</template>

<script>
export default {
  props: {
    count: {
      type: Number,
      required: false,
      default: 0,
    },
  },

  data() {
    return {
      query: '',
    };
  },

  mounted() {
    document.addEventListener('keydown', (event) => {
      let isEventSafe = (event) => {
        return event.target.tagName !== 'INPUT'
            && event.target.tagName !== 'TEXTAREA';
      };

      if (['/'].indexOf(event.key) !== -1 && isEventSafe(event)) {
        event.preventDefault();
        event.stopPropagation();
        this.$refs.search.focus();
      }
    });
  },

  watch: {
    query(value) {
      this.$emit('updated', value);
    },
  },
};
</script>