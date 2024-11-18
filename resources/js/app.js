import './bootstrap';
//import axios from 'axios';

//axios.defaults.baseURL = 'http://127.0.0.1:8000/api/v2/';
//axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');
//window.axios = axios;

//const {fetchPosts} = require('./posts/fetchPosts');
import fetchPosts from './posts/fetchPosts';
window.fetchPosts = fetchPosts;
//console.log('fetchPosts:', window.fetchPosts);

// window.fetchPosts = async () => {
//     const posts = await fetchPosts();
//     console.log("inside window");
//     console.log(posts); 
// };

