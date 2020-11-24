import axios from 'axios'

let headers = {
    'X-Requested-With': 'XMLHttpRequest',
    'cache-control': 'no-cache'
};

let accessToken = localStorage.getItem('api_token');

if (accessToken && accessToken !== '') {
    headers.Authorization = accessToken;
};

const request = axios.create({
    baseURL: "/api",
    headers: headers
});

// Add a request interceptor
request.interceptors.request.use(function (config) {
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
    return response;
}, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error

    // unauthorized
    if (error.response.status == 401) {
        // logout user
        localStorage.removeItem('api_token');
        localStorage.removeItem('current_user');
        window.location.href = "/";

        return;
    }

    console.log(error);

    return Promise.reject(error);
});

export default request