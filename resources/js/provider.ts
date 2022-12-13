import { InjectionKey, ref } from "vue";

export const globalKey: InjectionKey<ReturnType<typeof useGlobalProvider>> =
    Symbol("Global");

export const useGlobalProvider = () => {
    const isOpenNavigation = ref(true);

    return {
        isOpenNavigation,
    };
};
