import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useVuelidate from '@vuelidate/core'

/**
 * Composable function to handle dynamic form
 * It uses useForm and useVuelidate to manage form state
 * and validation rules
 *
 * @param {object} props - The props object
 * @param {function} schemaFn - A function that returns form structure and validation rules
 * @returns {object} - Object containing form and validation rules
*/
export function useDynamicForm(
    props: { item?: any },
    schemaFn: (props: any) => { formStructure: any; validationRules: any }
) {    // Form setup
    const { formStructure, validationRules } = schemaFn(props)
    const form = useForm(formStructure)

    // Vuelidate setup
    const $v = useVuelidate(validationRules, form)

    watch(
        () => props?.item,
        (newItem) => {
            const { formStructure: newForm, validationRules: newRules } = schemaFn({ item: newItem })

            // Update form
            Object.keys(newForm).forEach((key) => {
                form[key] = newForm[key]
            })

            // Update validation rules
            Object.keys(newRules).forEach((key) => {
                validationRules[key] = newRules[key]
            })

            $v.value.$reset()
        }
    )

    return {
        form,
        $v
    }
}
