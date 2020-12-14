import axios from 'axios'

axios.defaults.withCredentials = true;

const request = axios.create({
    baseURL: "/api",
    // headers: headers
});

// Add a request interceptor
request.interceptors.request.use(function (config) {
    if (!config.hasOwnProperty('toasterSuccess')) {
        config.toasterSuccess = true; // add toaster in success response
    }

    if (!config.hasOwnProperty('toasterError')) {
        config.toasterError = true; // add toaster in error response
    }
    
    // Do something before request is sent
    return config;
}, function (error) {
    // Do something with request error
    return Promise.reject(error);
});

// Add a response interceptor
request.interceptors.response.use(function (response) {
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    let toaster = response.config.toasterSuccess;
    let toasterMessage = response.config.toasterMessage || null;
    let responseMessage = "Success!";

    let methods = ['post', 'put', 'delete'];
    if (methods.indexOf(response.config.method) != -1 && toaster) {
        if (response.config.method == "post") {
            responseMessage = "Successfully added!";
        } else if (response.config.method == "put") {
            responseMessage = "Successfully updated!";
        } else if (response.config.method == "delete") {
            responseMessage = "Successfully deleted!";
        }

        // add toaster success
        Vue.toasted.show(toasterMessage ? toasterMessage : responseMessage, {
            action: {
                icon: 'close',
                iconPack: 'mdi',
                onClick:(e, toast) => {
                    toast.goAway(0);
                }
            },
            type: 'success',
            iconPack: 'mdi',
            icon: 'mdi-check',
            keepOnHover: true,
            duration: 5000
        });
    }

    return response;
}, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    let toaster = error.response.config.toasterError;

    if (error.response.status == 401) {
        // unauthorized
        // logout user
        localStorage.removeItem('authenticated');
        // localStorage.removeItem('current_vendor');
        window.location.href = "/";

        return;
    } else if (error.response.status == 422 && toaster) {
        // validate error
        let message = error.response.data.message;
        let errors = error.response.data.errors;
        for(let index in errors) {
            message = errors[index][0];
        }

        Vue.toasted.show(message, {
            action: {
                icon: 'close',
                iconPack: 'mdi',
                onClick:(e, toast) => {
                    toast.goAway(0);
                }
            },
            type: 'error',
            iconPack: 'mdi',
            icon: 'mdi-alert',
            keepOnHover: true,
            duration: 5000
        });
    } else if (error.response.status != 403 && toaster) {
        // not equals to error (vendor not verified)
        Vue.toasted.show(error.response.data.message, {
            action: {
                icon: 'close',
                iconPack: 'mdi',
                onClick:(e, toast) => {
                    toast.goAway(0);
                }
            },
            type: 'error',
            iconPack: 'mdi',
            icon: 'mdi-alert',
            keepOnHover: true,
            duration: 5000
        });
    }

    return Promise.reject(error);
});

export default request