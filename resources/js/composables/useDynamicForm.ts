import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import useVuelidate from '@vuelidate/core';

export function useDynamicForm<
    T extends Record<string, any>,
    P extends { item?: Record<string, any> | null }
>(
    props: P,
    schemaFn: (props: P) => {
        formStructure: T;
        validationRules: any;
    }
) {
    const { formStructure, validationRules } = schemaFn(props);
    const form = useForm<T>(formStructure);

    const $v = useVuelidate(validationRules, form);

    watch(
        () => props.item?.id,
    () => {
            const { formStructure: newForm } = schemaFn({ item: props.item } as P);

            form.defaults(newForm as T);
            form.reset();
            $v.value.$reset();
        }
    );

    return {
        form,
        $v
    };
}
