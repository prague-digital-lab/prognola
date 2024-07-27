import useSWR from 'swr';
import { fetcher } from '@/libs/utils/api';

export const useTask = (uuid: string, token?: string | null) => {
    const pathKey = `/tasks/${uuid}`;

    const swr = useSWR([pathKey, token], (([url, token]) => fetcher(url, token)), {
        refreshInterval: 10000
    });

    return swr
};