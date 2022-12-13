import { InjectionKey, inject } from "vue";

export const strictInject = <T>(key: InjectionKey<T>) => {
    return inject(key) as T;
};
