<template>
  <div class="modal fade" id="movieModal" tabindex="-1" v-if="open">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <movie-info :movie="movie" :full="true"></movie-info>
        </div>
        <div class="modal-footer">
          <a :href="`file://${movie.path}`" class="btn btn-sm btn-danger" target="_blank">Lire le film</a>
          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    movie: {
      type: Object,
      required: false,
      default: null,
    },
  },

  data() {
    return {
      open: false,
    };
  },

  methods: {
    show() {
      this.open = true;

      this.$nextTick(() => {
        $('#movieModal').modal();
        $('#movieModal').on('hidden.bs.modal', (event) => {
          this.open = false;
          this.$emit('closed');
        });
      });
    },
  },

  watch: {
    movie(value) {
      if (value) {
        this.show();
      }
    },
  },
};
</script>