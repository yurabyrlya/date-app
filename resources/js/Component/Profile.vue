<template>
  <div class="container">
    <div class="page-content page-container mt-5" >
      <div class="padding">
        <div class="row">
          <div class="col-md-6 m-auto">
            <div
              class="alert"
              :class="alertType"
              role="alert">
              {{message}}
            </div>
            <div class="card">
              <h6 class="text-center">Filters by age: from - to</h6>
              <form class="form-inline p-3" @submit.prevent>
                <div class="form-group mx-sm-3 mb-2">
                  <label for="ageFrom" class="sr-only">from</label>
                  <input type="number"  v-model="age.from" class="form-control" id="ageFrom" placeholder="18" >
                </div>
                <div class="form-group mx-sm-3 mb-2">
                  <label for="ageTo" class="sr-only">Age To</label>
                  <input type="number" v-model="age.to" class="form-control" id="ageTo" placeholder="45">
                </div>
                <button type="submit" @click.prevent="getRandomProfile" class="btn btn-primary mb-2 m-auto">Update</button>
              </form>
            </div>
            <div class="card" v-if="notFound === false">
              <div class="card-header text-center"><h2>Profile</h2></div>
              <div class="py-5 h-100">
                <div class="row d-flex  h-100">
                  <div class="col col-md-9 col-lg-7 col-xl-5 ml-4">
                    <div class="card" style="border-radius: 15px;">
                      <div class="card-body p-4">
                        <div class="d-flex text-black">
                          <div class="flex-shrink-0">
                            <img src="https://cdn-icons-png.flaticon.com/512/1250/1250689.png"
                                 alt="Generic placeholder image" class="img-fluid"
                                 style="width: 180px; border-radius: 10px;">
                          </div>
                          <div class="flex-grow-1 ms-3 ml-5">
                            <h5 class="mb-1">{{profile.name}}</h5>
                            <p class="mb-2 pb-1" style="color: #2b2a2a;">{{profile.gender}}</p>
                            <div class="d-flex justify-content-start rounded-3 p-2 mb-2"
                                 style="background-color: #efefef;">
                              <div>
                                <p class="small text-muted mb-1">Age</p>
                                <p class="mb-0">{{profile.age}}</p>
                              </div>
                              <div class="px-3">
                                <p class="small text-muted mb-1">Followers</p>
                                <p class="mb-0">976</p>
                              </div>
                              <div>
                                <p class="small text-muted mb-1">Rating</p>
                                <p class="mb-0">8.5</p>
                              </div>
                            </div>
                            <div class="d-flex pt-1">
                              <button
                                  @click="next('NO')"
                                  type="button" class="btn btn-outline-primary me-1 flex-grow-1">Skip</button>
                              <button
                                  @click="next('YES')"
                                  type="button" class="btn ml-2 btn-danger flex-grow-1">Like</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
            }else {
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