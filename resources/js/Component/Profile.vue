<template>
  <div class="container" id="profile">
    <div class="page-content page-container mt-5">
      <div class="padding">
        <div class="row">
          <section
              class="col-md-12 vh-100"
              style="background-color: #eee;"
          >
            <div class="container py-5 h-100">
              <div class="row justify-content-center">
                <div
                    class="alert col-xl-4"
                    :class="alertType"
                    role="alert"
                >
                  {{message}}
                </div>
              </div>
              <div class="row d-flex justify-content-center h-100">
                <div class="col-md-12 col-xl-4 " v-if="!notFound">
                  <div class="card" style="border-radius: 15px;">
                    <div class="card-body text-center">
                      <div class="mt-3 mb-4">
                        <img :src="profile.img"
                             class="rounded-circle img-fluid profile-img"  />
                      </div>
                      <div class="mt-5 mb-3">
                        <h4 class="mb-2">{{profile.name}}, {{profile.age}} </h4>
                      </div>
                    </div>
                  </div>
                  <div class="mt-4 pb-2 text-center">
                    <button
                      @click="next('YES')"
                      type="button"
                      class="btn btn-lg btn-outline-danger btn-floating mr-2"
                    >
                      <i class="bi bi-emoji-heart-eyes"></i>
                    </button>
                    <button
                      @click="next('NO')"
                      type="button"
                      class="btn btn-lg btn-outline-secondary btn-floating ml-2"
                    >
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
    <a
      id="open-filter"
      class="btn btn-light"
      data-bs-toggle="offcanvas"
      href="#offcanvas"
      role="button"
      aria-controls="offcanvas"
    >
      <i class="bi bi-arrow-right-square"></i>
    </a>
    <div
      class="offcanvas offcanvas-start"
      tabindex="-1"
      id="offcanvas"
      aria-labelledby="offcanvasLabel"
    >
      <div class="offcanvas-header">
        <h5
          class="offcanvas-title"
          id="offcanvasLabel"
        >Filters</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="col-md-11 m-auto">
          <h6 class="text-center">Age range</h6>
          <form class="form-inline p-3" @submit.prevent>
              <div class="form-group mx-sm-3 mb-2">
                <label for="ageFrom" class="sr-only">From:</label>
                <input
                  type="number"
                  v-model="age.from"
                  class="form-control"
                  id="ageFrom"
                  placeholder="18">
              </div>
              <div class="form-group mx-sm-3 mb-2" style="text-align: left">
                <label for="ageTo" class="sr-only">To:</label>
                <input
                  type="number"
                  v-model="age.to"
                  class="form-control"
                  id="ageTo"
                  placeholder="45"
                >
              </div>
              <div class="form-group text-center mt-5">
                <button
                    type="submit"
                    @click.prevent="getRandomProfile"
                    class="btn btn-primary mb-2 m-auto"
                >
                  Update
                </button>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const axios = require('axios');

export default {
  name: "Profile",
  data() {
    return {
      profile: {
        name: '',
        gender: '',
        age: null,
      },
      message: '',
      alertType: 'alert-primary',
      notFound: false,
      age: {
        from: 18,
        to: 140,
      }
    }
  },
  methods: {
    next(like) {
      axios.post('/swipe', {
        token: localStorage.getItem('token'), // to improve to use auth headers
        profile_id: this.profile.id,
        preference: like,
      }).then((response) => {
            const status = response.data.status;
            if (status === 404 || status === 401 ) {
              this.alertType = 'alert-danger';
              this.message = response.data.message;
            }
            this.alertType = 'alert-primary';
            if (response.data.match === 'Yes') {
              this.message = 'Match';
            } else {
              this.getRandomProfile();
            }

          })
    },
    getRandomProfile(){
      this.message = '';
      const token =  localStorage.getItem('token');
      const  filter = new URLSearchParams(this.age).toString()
      axios.get('/profiles?token='+ token + '&' + filter) // to improve to use auth headers
          .then((response) => {
            const status = response.data.status;
            if (status === 404 || status === 401 ) {
              this.alertType = 'alert-danger';
              this.message = response.data.message;
              this.notFound = true
              return;
            }
            this.notFound = false;
            this.alertType = 'alert-primary';
            this.profile  = response.data
            let randInt = Math.floor(Math.random() * 4);
            if (randInt === 0) randInt = 1;
            this.profile.img = 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava'+randInt +'-bg.webp'
          })
    }
  },
  mounted() {
    this.getRandomProfile()
  }
}
</script>

<style scoped>

</style>