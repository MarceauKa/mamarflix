<template>
  <div class="d-flex flex-column sticky-top" style="top: 10px;">
    <h6 class="mb-1">Affichage</h6>
    <div class="btn-group btn-group-sm mb-3">
      <button class="btn"
              :class="{'btn-danger': display === 'grid', 'btn-outline-secondary': display !== 'grid'}"
              @click.prevent="display = 'grid'">
        Grille
      </button>
      <button class="btn"
              :class="{'btn-danger': display === 'list', 'btn-outline-secondary': display !== 'list'}"
              @click.prevent="display = 'list'">
        Liste
      </button>
    </div>

    <h6 class="mb-1">Trier par</h6>
    <div class="btn-group-vertical btn-group-sm mb-3">
      <button class="btn btn-block"
              :class="{'btn-danger': sort === 'name', 'btn-outline-secondary': sort !== 'name'}"
              @click.prevent="sort = 'name'">
        Nom
      </button>
      <button class="btn btn-block mr-2"
              :class="{'btn-danger': sort === 'date', 'btn-outline-secondary': sort !== 'date'}"
              @click.prevent="sort = 'date'">
        Date
      </button>
      <button class="btn btn-block"
              :class="{'btn-danger': sort === 'note', 'btn-outline-secondary': sort !== 'note'}"
              @click.prevent="sort = 'note'">
        Note
      </button>
    </div>

    <h6 class="mb-1">Ordre de tri</h6>
    <div class="btn-group btn-group-sm mb-3">
      <button class="btn"
              :class="{'btn-danger': direction === 'asc', 'btn-outline-secondary': direction !== 'asc'}"
              @click.prevent="direction = 'asc'">
        Asc
      </button>
      <button class="btn"
              :class="{'btn-danger': direction === 'desc', 'btn-outline-secondary': direction !== 'desc'}"
              @click.prevent="direction = 'desc'">
        Desc
      </button>
    </div>

    <h6 class="mb-1 d-flex justify-content-between align-items-center">
      Genre
      <a @click.prevent="filteredGenres = []"
         v-if="filteredGenres.length > 0"
         class="small"
         href="#">
        Effacer
      </a>
    </h6>
    <div class="d-flex flex-column">
      <div class="form-group">
        <select v-model="filteredGenres"
                class="form-control"
                :size="genres.length"
                multiple>
          <option v-for="(genre, index) in genres"
                  :key="index"
                  :value="genre"
          >{{ genre }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    genres: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      display: 'grid',
      sort: 'name',
      direction: 'asc',
      filteredGenres: [],
    };
  },

  methods: {
    update() {
      this.$emit('updated', {
        display: this.display,
        sort: this.sort,
        direction: this.direction,
        genres: this.filteredGenres,
      });
    },
  },

  watch: {
    display(value) {
      this.update();
    },

    sort() {
      this.update();
    },

    direction() {
      this.update();
    },

    filteredGenres() {
      this.update();
    },
  },
};
</script>