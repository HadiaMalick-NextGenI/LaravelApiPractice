import axios from 'axios';

const fetchPosts = async () => {
    console.log("inside fetch posts");
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/v2/posts');
        console.log(response.data);
        return response.data;
    } catch (error) {
        console.error("There was an error fetching posts:", error);
        return error;
    }
};

export default fetchPosts;