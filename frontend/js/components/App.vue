<template>
  <div>
    <search :count="movies.length"
            @updated="query = $event"
    ></search>

    <modal :movie="selected"
           @closed="selected = null"
    ></modal>

    <div class="row">
      <div class="col-12 col-md-4 col-lg-2">
        <filters :genres="genres"
                 @updated="filters = $event"
        ></filters>
      </div>

      <div class="col-12 col-md-8 col-lg-10">
        <div v-if="countResults > 0 && filters.display === 'list'">
          <div v-for="result in filtered"
               :v-key="result.id"
               class="row mb-4 movie-list-item"
          >
            <div class="col-12 col-md-4 col-lg-3 position-relative">
              <v-lazy-image :src="result.poster_file"
                            class="card-img shadow"
                            :alt="result.title"/>
              <span :class="`badge position-absolute shadow ${result.note_class}`"
                    style="top: .5rem; left: 1.5rem">
                {{ result.note }}
              </span>
            </div>

            <div class="col-12 col-md-8 col-lg-9">
              <div class="card shadow">
                <movie-info :movie="result" class="card-body d-flex flex-column align-items-stretch"></movie-info>
              </div>
            </div>
          </div>
        </div>

        <div v-if="countResults > 0 && filters.display === 'grid'">
          <div class="row">
            <div v-for="result in filtered"
                 :v-for="result.key"
                 class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 movie-grid-item"
                 @click.prevent="selected = result">
              <keep-alive>
                <div class="position-relative">
                  <v-lazy-image :src="result.poster_file"
                                class="card-img shadow"
                                :alt="result.title"/>
                  <span :class="`badge position-absolute shadow ${result.note_class}`"
                        style="top: .5rem; left: .5rem">
                    {{ result.note }}
                  </span>
                </div>
              </keep-alive>
            </div>
          </div>
        </div>

        <div v-else>
          <p class="lead text-center">
            Aucun résultat ne correspond à la recherche
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      movies: [],
      query: '',
      genres: [],
      filters: {
        display: 'grid',
        sort: 'name',
        direction: 'asc',
        genres: [],
      },
      selected: null,
    };
  },

  mounted() {
    this.movies = m2s
    this.movies.forEach((item) => {
      item.note_class = this.noteClass(item.note)
    })
    this.getGenres();
  },

  methods: {
    getGenres() {
      let genres = [];

      this.movies.forEach((movie) => {
        movie.genres.forEach((genre) => {
          if (genres.indexOf(genre) === -1) {
            genres.push(genre);
          }
        });
      });

      genres = genres.sort((a, b) => {
        if (a > b) return 1;
        else if (a < b) return -1;
        else return 0;
      });

      this.genres = genres;
    },

    noteClass(note) {
      note = parseFloat(note);

      if (note < 10 && note >= 7.5) {
        return 'badge-success text-white';
      } else if (note < 7.5 && note >= 5.5) {
        return 'badge-primary text-white';
      } else {
        return 'badge-warning text-white';
      }
    },

    slug(string) {
      string = string.replace(/^\s+|\s+$/g, '');
      string = string.toLowerCase();

      let from = 'ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;';
      let to = 'aaaaaeeeeeiiiiooooouuuunc------';

      for (let i = 0, l = from.length; i < l; i++) {
        string = string.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
      }

      string = string.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');

      return string;
    },
  },

  computed: {
    filtered() {
      let movies = this.movies;
      let sort = this.filters.sort;
      let direction = this.filters.direction;
      let query = this.query;
      let genres = this.filters.genres;

      if (query) {
        let regexp = new RegExp(`${query}`, 'giu');

        movies = movies.filter((item) => {
          let title = item.title || '';
          let original = item.original || '';
          let format = item.original || '';
          let resume = item.resume || '';
          return title.match(regexp) !== null
              || original.match(regexp) !== null
              || format.match(regexp) !== null
              || resume.match(regexp) !== null;
        });
      }

      if (genres.length > 0) {
        genres.forEach((genre) => {
          movies = movies.filter((item) => {
            return item.genres.includes(genre);
          });
        });
      }

      movies = movies.sort((a, b) => {
        switch (sort) {
          case 'name':
            a = this.slug(a.title);
            b = this.slug(b.title);
            if (a > b) return 1;
            else if (a < b) return -1;
            else return 0;

          case 'note':
            return parseFloat(a.note) - parseFloat(b.note);

          case 'date':
            a = new Date(a.date).getTime();
            b = new Date(b.date).getTime();
            return a - b;
        }
      });

      if (direction === 'desc') {
        movies = movies.reverse();
      }

      return movies;
    },

    countResults() {
      return this.filtered.length;
    },

    countMovies() {
      return this.movies.length;
    },
  },
};
</script>