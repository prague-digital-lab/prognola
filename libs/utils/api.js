import axios from 'axios';
import config from '@/config';

export const instance = axios.create({
    baseURL: config.API_HOST,
    timeout: 40000,
    headers: {
        Accept: 'application/json, text/plain, */*',
        'Content-Type': 'application/json; charset=utf-8',
        // Authorization: (token.length > 0) ? `Bearer ${token}` : ``
    }
});

export const fetcher = (url, token) => {
    return instance.get(url, token ? { headers: { Authorization: "Bearer " + token } } : {}).then((res) => {
        if (!res.data) {
            throw Error(res.data.message);
        }

        return res.data;
    });
};

export default instance;