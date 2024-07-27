import useSWR from 'swr';
import { fetcher } from '@/libs/utils/api';

export const useTasks = (token?:string|null) => {
    const pathKey = `/tasks`;

    const swr = useSWR([pathKey, token], (([url, token]) => fetcher(url, token)), {
        refreshInterval: 10000
    });

    return swr
};