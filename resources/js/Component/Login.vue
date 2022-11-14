<template>
  <div id="login" class="flex">
      <div class="page-content page-container" id="page-content">
        <div class="padding">
          <div class="row m-auto">
            <div class="col-4">
              <button
                  @click="generate"
                  type="submit"
                  class="btn btn-danger">Create new user</button>
            </div>
            <div class="col-6">
              <table class="table">
                <thead>
                <tr>
                  <th scope="col">Name</th>
                  <th scope="col">Gender</th>
                  <th scope="col">Email</th>
                  <th scope="col">Password</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                  <td>{{newUser.name}}</td>
                  <td>{{newUser.gender}}</td>
                  <td>{{newUser.email}}</td>
                  <td>{{newUser.password}}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 m-auto">
              <div class="card">
                <div
                    class="alert"
                    :class="alertType"
                    role="alert">
                  {{message}}
                </div>
                <div class="card-header text-center"><h2>Login </h2></div>
                <div class="card-body">
                  <form @submit.prevent>
                    <div class="form-group">
                      <label class="text-muted" for="email">Email address</label>
                      <input
                        v-model="email"
                        type="email"
                        class="form-control"
                        id="email"
                        placeholder="Enter email"
                        required
                      >
                    </div>
                    <div class="form-group">
                      <label
                          class="text-muted"
                          for="password">
                        Password
                      </label>
                      <input
                          v-model="password"
                          type="password"
                          class="form-control"
                          id="password"
                          placeholder="Password"
                          required
                      >
                    </div>
                    <button
                        @click="login"
                        type="submit"
                        class="btn btn-primary">Let's date</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<script>

import axios from "axios";

export default {
  name: "Login",
  data() {
    return {
      email: '',
      password:'',
      message: '',
      alertType: 'alert-primary',
      newUser: {
          email: '',
          password: '',
          name: '',
          gender: '',
          age: null,
      }
    }
  },
  methods: {
    generate (){
      const axios = require('axios');
      axios.get('/user/create').then((response) => {
        this.newUser = response.data
      })
    },
    login(){
      const axios = require('axios');
      axios.post('/login', {
        email: this.email,
        password: this.password
      }).then((response) => {
        const status = response.data.status;
        if (status === 404 || status === 401 ) {
          this.alertType = 'alert-danger';
          this.message = response.data.message;
        } else {
          this.alertType = 'alert-primary';
          this.message = response.data
          if (response.data.token){
            localStorage.setItem('token', response.data.token);
            this.$router.push({
              path: '/profile',
            })
          }
        }
      })
    }
  }
}
</script>
