import { watch, computed } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

const $toast = useToast();

export function convertDataToSelectOptions(data) {
    return computed(() =>
        data.map(data => ({
            label: data.name,
            value: String(data.id)
        }))
    );
}

export function useDebouncedFilter(filters, routeName, routeParams = {}, delay = 500) {
    watch(
        () => filters.map(filter => filter.value.value),
        debounce(() => {
            const params = Object.fromEntries(
                filters.map(filter => [filter.name, filter.value.value])
            );

            router.get(route(routeName, routeParams), params, { preserveState: true });
        }, delay)
    );
}

export const formatDate = (date) => {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
    };

    return new Date(date).toLocaleDateString('en-US', options);
};

export function formatNumber(num) {
    if (num >= 1e9) {
        return (num / 1e9).toFixed(1) + "B"; // Billions
    } else if (num >= 1e6) {
        return (num / 1e6).toFixed(1) + "M"; // Millions
    } else if (num >= 1e3) {
        return (num / 1e3).toFixed(1) + "K"; // Thousands
    }
    return num.toString();
}

export function toast(message, type) {
    if (type === 'success') {
        $toast.success(message)
    } else if (type === 'error') {
        $toast.error(message)
    }
}