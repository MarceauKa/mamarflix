<template>
  <div class="d-flex flex-column sticky-top" style="top: 10px;">
    <h6 class="mb-1">Affichage</h6>
    <div class="d-flex mb-3">
      <button class="btn btn-sm mr-2"
              :class="{'btn-primary': display === 'grid', 'btn-outline-secondary': display !== 'grid'}"
              @click.prevent="display = 'grid'">
        Grille
      </button>
      <button class="btn btn-sm"
              :class="{'btn-primary': display === 'list', 'btn-outline-secondary': display !== 'list'}"
              @click.prevent="display = 'list'">
        Liste
      </button>
    </div>

    <h6 class="mb-1">Trier par</h6>
    <div class="mb-3">
      <button class="btn btn-sm btn-block"
              :class="{'btn-primary': sort === 'name', 'btn-outline-secondary': sort !== 'name'}"
              @click.prevent="sort = 'name'">
        Nom
      </button>
      <button class="btn btn-sm btn-block mr-2"
              :class="{'btn-primary': sort === 'date', 'btn-outline-secondary': sort !== 'date'}"
              @click.prevent="sort = 'date'">
        Date
      </button>
      <button class="btn btn-sm btn-block"
              :class="{'btn-primary': sort === 'note', 'btn-outline-secondary': sort !== 'note'}"
              @click.prevent="sort = 'note'">
        Note
      </button>
    </div>

    <h6 class="mb-1">Ordre de tri</h6>
    <div class="mb-3">
      <button class="btn btn-sm mr-2"
              :class="{'btn-primary': direction === 'asc', 'btn-outline-secondary': direction !== 'asc'}"
              @click.prevent="direction = 'asc'">
        Asc
      </button>
      <button class="btn btn-sm mr-"
              :class="{'btn-primary': direction === 'desc', 'btn-outline-secondary': direction !== 'desc'}"
              @click.prevent="direction = 'desc'">
        Desc
      </button>
    </div>

    <h6 class="mb-1">Genre</h6>
    <div class="d-flex flex-column">
      <div v-for="(genre, index) in genres" class="form-check">
        <input class="form-check-input" type="checkbox" :value="genre" :id="`genre${index}`" v-model="filteredGenres">
        <label class="form-check-label" :for="`genre${index}`">{{ genre }}</label>
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
      display: 'list',
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
        genres: this.filteredGenres
      });
    },
  },

  watch: {
    display(value) {
      this.update()
    },

    sort() {
      this.update()
    },

    direction() {
      this.update()
    },

    filteredGenres() {
      this.update()
    }
  },
};
</script>