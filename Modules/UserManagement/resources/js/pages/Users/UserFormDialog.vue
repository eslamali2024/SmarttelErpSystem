<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogScrollContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { useI18n } from 'vue-i18n';
import Button from '@/components/ui/button/Button.vue';
import SelectSearchable from '@/components/ui/SelectSearchable.vue';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs"
import {
    Card,
    CardContent
} from "@/components/ui/card"
import RoleModules from '@modules/UserManagement/resources/js/components/RoleModules.vue';
import { required, email, minLength, maxLength } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: {
        user?: any,
        roles?: any[],
        permissions?: any[]
    },
    roles?: string[],
    permissions?: any,
    loading?: boolean
}>();

const { t } = useI18n();
const emit = defineEmits(['update:show']);
let roleOptions = props.roles?.map((role) => ({ value: role, label: role })) || [];
const isReadOnly = computed(() => props.method_type === 'show')
const form = useForm({
    name: props.item?.user?.name ?? '',
    email: props.item?.user?.email ?? '',
    roles: Array.isArray(props.item?.roles) ? props.item.roles : [],
    permissions: props.item?.permissions ?? []

});

// Vuelidate
const $v = useVuelidate({
    name: { required, minLength: minLength(5), maxLength: maxLength(255) },
    email: { required, email },
    roles: {
        $each: { required }
    },
}, form)

watch(() => props.item, (newItem) => {
    if (newItem) {
        form.name = newItem?.user?.name ?? '';
        form.email = newItem?.user?.email ?? '';
        form.roles = newItem?.roles ?? [];
        form.permissions = newItem?.permissions ?? [];

        roleOptions = props.roles?.map((role) => ({ value: role, label: role })) || [];
    }
    $v.value.$reset();
});

/**
 * Submits the form data to the server.
 * If the form is in read-only mode, does nothing.
 * If the form is in create mode, sends a POST request to the server.
 * If the form is in update mode, sends a PUT request to the server.
 * On success, emits an event to close the dialog and resets the form data.
 * On error, logs the error to the console.
 * Preserves the scroll position of the page.
 */
const submitForm = () => {
    $v.value.$touch()
    if (isReadOnly.value || $v.value.$invalid) return;

    const currentPage = new URLSearchParams(window.location.search).get('page') || 1;

    const options = {
        onSuccess: () => {
            emit('update:show', false);
            form.reset();
            $v.value.$reset();
        },
        onError: (e: any) => {
            console.error(e);
        },
        preserveScroll: true,
    };

    const actionUrl = props.action.includes('?')
        ? `${props.action}&page=${currentPage}`
        : `${props.action}?page=${currentPage}`;

    if (props.method_type === 'put') {
        form.put(actionUrl, options);
    }
};

// Get Title Dialog
const title = computed(() => {
    switch (props.method_type) {
        case 'post':
            return t('add_user')
        case 'put':
            return t('update_user')
        case 'show':
            return t('view_user')
        default:
            return t('add_user')
    }
})
</script>

<template>
    <form @action.prevent="submitForm">

        <Dialog :open="show" @update:open="val => emit('update:show', val)">
            <DialogScrollContent class="sm:max-w-[1000px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ title }}
                    </DialogTitle>
                </DialogHeader>
                <DialogDescription :loading="loading">
                    <div class="grid grid-cols-1 gap-3 py-4">
                        <InputGroup v-model="form.name" :modelValueError="form.errors.name" :vueError="$v?.name"
                            :label="$t('name')" :placeholder="$t('please_enter_a_name')" type="text"
                            :disabled="isReadOnly" />

                        <InputGroup v-model="form.email" :modelValueError="form.errors.email" :vueError="$v?.email"
                            :label="$t('email')" :placeholder="$t('please_enter_a_email')" type="text"
                            :disabled="isReadOnly" />

                        <SelectSearchable :items="roleOptions" v-model:modelValue="form.roles"
                            :modelValueError="form.errors.roles" :label="$t('roles')"
                            :placeholder="$t('please_select_roles')" :disabled="isReadOnly" />

                        <div v-if="props?.permissions">
                            <hr class="w-96 mx-auto my-2">

                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $t('permissions') }}
                                </h3>
                            </div>

                            <Tabs :default-value="Object.keys(props?.permissions || {})[0]" class="w-full flex gap-2"
                                orientation="vertical">
                                <TabsList class="w-full md:w-1/3 flex flex-col gap-2 justify-start items-stretch">
                                    <TabsTrigger v-for="(module, key) in props.permissions" :key="'module-' + key"
                                        :value="key">
                                        {{ key }}
                                    </TabsTrigger>
                                </TabsList>
                                <div class="w-full md:w-2/3  h-[400px] overflow-y-auto">
                                    <TabsContent v-for="(module, key) in props.permissions" :key="'content-' + key"
                                        :value="key">
                                        <Card>
                                            <CardContent class="space-y-2">
                                                <RoleModules :module="module" :level="0" v-model="form.permissions"
                                                    :isReadOnly="isReadOnly" />
                                            </CardContent>
                                        </Card>
                                    </TabsContent>
                                </div>
                            </Tabs>
                        </div>
                    </div>
                </DialogDescription>
                <DialogFooter>
                    <ButtonSubmit :loading="form.processing" :submit="submitForm" v-if="!isReadOnly"
                        :disabled="props.loading" :cancel="() => emit('update:show', false)">
                        {{ $t('save') }}
                    </ButtonSubmit>
                    <Button v-else type="button" @click="() => emit('update:show', false)" class="cursor-pointer">
                        {{ $t('close') }}
                    </Button>
                </DialogFooter>
            </DialogScrollContent>
        </Dialog>

    </form>

</template>
