<template>
  <div class="movie-info">
    <div class="mb-2 d-flex justify-content-between align-items-start">
      <div>
        <h5 class="card-title mb-1">{{ movie.title }}</h5>

        <h6 class="card-title text-muted mb-0" v-if="movie.title !== movie.original_title">
          {{ movie.original_title }}
        </h6>
      </div>

      <span class="badge badge-danger">{{ movie.date }}</span>
    </div>

    <blockquote class="blockquote flex-grow-1">
      <p class="mb-0">{{ movie.resume }}</p>
    </blockquote>

    <div class="row" v-if="more">
      <div class="col-12">
        <div v-if="movie.genres">
          <div class="font-weight-bold small mb-0">Genres</div>
          <div class="text-muted small mb-1">{{ movie.genres.join(', ') }}</div>
        </div>

        <div v-if="movie.casting">
          <div class="font-weight-bold small mb-0">Casting</div>
          <div class="text-muted small mb-1">{{ movie.casting.join(', ') }}</div>
        </div>

        <div v-if="movie.audio">
          <div class="font-weight-bold small mb-0">Audio</div>
          <div class="text-muted small mb-1">{{ movie.audio.join(', ') }}</div>
        </div>

        <div v-if="movie.subtitles">
          <div class="font-weight-bold small mb-0">Sous-titres</div>
          <div class="text-muted small mb-1">{{ movie.subtitles.join(', ') }}</div>
        </div>

        <div class="d-flex flex-row justify-content-start">
          <div class="mr-2">
            <div class="font-weight-bold small mb-0">Fichier</div>
            <div class="text-muted small mb-0">{{ movie.file }}</div>
          </div>

          <div class="mr-2" v-if="movie.duration">
            <div class="font-weight-bold small mb-0">Durée</div>
            <div class="text-muted small mb-1">{{ movie.duration }}</div>
          </div>

          <div class="mr-2" v-if="movie.format">
            <div class="font-weight-bold small mb-0">Format</div>
            <div class="text-muted small mb-1">
              {{ movie.format }}
              <span v-if="movie.hdr">(HDR)</span>
            </div>
          </div>

          <div v-if="movie.size">
            <div class="font-weight-bold small mb-0">Poids</div>
            <div class="text-muted small mb-0">{{ movie.size }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center small" v-if="full === false">
      <a href="#" @click.prevent="more = true" v-if="more === false">Afficher plus</a>
      <a href="#" @click.prevent="more = false" v-else>Afficher moins</a>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    movie: {
      type: Object,
      required: true,
    },
    full: {
      type: Boolean,
      required: false,
      default: false,
    }
  },

  data() {
    return {
      more: false,
    }
  },

  mounted() {
    if (this.full) {
      this.more = true
    }
  }
};
</script>