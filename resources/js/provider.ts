import { InjectionKey, ref, Ref } from "vue";
import axios from "axios";

import { ErrorCount, ErrorList, SDCount, SDList } from "./types";

export const globalKey: InjectionKey<ReturnType<typeof useGlobalProvider>> =
    Symbol("Global");

export const useGlobalProvider = () => {
    const isOpenNavigation = ref(true);

    const isErrorCountLoading = ref<boolean>(false);
    const isErrorListLoading = ref<boolean>(false);
    const isSDCountLoading = ref<boolean>(false);
    const isSDListLoading = ref<boolean>(false);

    const errorCounts: Ref<ErrorCount[]> = ref([]);
    const errorList: Ref<ErrorList[]> = ref([]);
    const sdCounts: Ref<SDCount[]> = ref([]);
    const sdList: Ref<SDList[]> = ref([]);

    const fetchErrorCount = async () => {
        isErrorCountLoading.value = true;
        const { data } = await axios.get<ErrorCount[]>("/api/errors/count");
        errorCounts.value = data;
        isErrorCountLoading.value = false;
    };

    const fetchErrorList = async () => {
        isErrorListLoading.value = true;
        const { data } = await axios.get<ErrorList[]>("/api/errors/list");
        errorList.value = data;
        isErrorListLoading.value = false;
    };

    const fetchSDCount = async () => {
        isSDCountLoading.value = true;
        const { data } = await axios.get<SDCount[]>("/api/sds/count");
        sdCounts.value = data;
        isSDCountLoading.value = false;
    };

    const fetchSDList = async () => {
        isSDListLoading.value = true;
        const { data } = await axios.get<SDList[]>("/api/sds/list");
        sdList.value = data;
        isSDListLoading.value = false;
    };

    return {
        isOpenNavigation,
        isErrorCountLoading,
        isErrorListLoading,
        isSDCountLoading,
        isSDListLoading,
        errorCounts,
        errorList,
        sdCounts,
        sdList,
        fetchErrorCount,
        fetchErrorList,
        fetchSDCount,
        fetchSDList,
    };
};
