<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogScrollContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import {
    Card,
    CardContent,
    CardHeader,
} from "@/components/ui/card"

import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs"
import RoleModules from '@modules/UserManagement/resources/js/components/RoleModules.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: {
        role?: any,
        permissions?: any[]
    },
    permissions?: any
}>();

const emit = defineEmits(['update:show']);

const form = useForm({
    name: props.item?.role?.name ?? '',
    permissions: props.item?.permissions ?? []
});

watch(() => props.item, (newItem) => {
    form.name = newItem?.role?.name ?? '';
    form.permissions = newItem?.permissions ?? [];
});

const submitForm = () => {
    const options = {
        onSuccess: () => {
            emit('update:show', false)
            form.reset();
        },
        onerror: (e: any) => {
            console.log(e)
        }
    };

    if (props.method_type === 'post') {
        form.post(props.action, options);
    } else if (props.method_type === 'put') {
        form.put(props.action, options);
    }
};
</script>

<template>
    <form @action.prevent="submitForm">

        <Dialog :open="show" @update:open="val => emit('update:show', val)">
            <DialogScrollContent class="sm:max-w-[1000px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ props.method_type === 'post' ? $t('add_role') : $t('update_role') }}
                    </DialogTitle>
                </DialogHeader>
                <DialogDescription>
                    <div class="grid grid-cols-1 gap-3 py-4">
                        <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                            :placeholder="$t('please_enter_a_name')" type="text" />
                        <p v-if="form.errors.permissions" class="text-sm text-red-500">
                            {{ form.errors.permissions }}
                        </p>

                        <div v-if="props?.permissions">
                            <Tabs :default-value="Object.keys(props?.permissions || {})[0]" class="w-full flex gap-2"
                                orientation="vertical">
                                <TabsList class="w-full md:w-1/3 flex flex-col gap-2 justify-start items-stretch">
                                    <TabsTrigger v-for="(module, key) in props.permissions" :key="'module-' + key"
                                        :value="key">
                                        {{ key }}
                                    </TabsTrigger>
                                </TabsList>
                                <div class="w-full md:w-2/3">
                                    <TabsContent v-for="(module, key) in props.permissions" :key="'content-' + key"
                                        :value="key">
                                        <Card>
                                            <CardHeader class="flex justify-between gap-2 items-center">
                                                <InputGroup :label="$t('search')"
                                                    :placeholder="$t('please_enter_a_name')" type="text" />
                                            </CardHeader>
                                            <CardContent class="space-y-2">
                                                <RoleModules :module="module" :level="0" v-model="form.permissions" />
                                            </CardContent>
                                        </Card>
                                    </TabsContent>
                                </div>
                            </Tabs>
                        </div>
                    </div>
                </DialogDescription>
                <DialogFooter>
                    <ButtonSubmit :loading="form.processing" :submit="submitForm"
                        :cancel="() => emit('update:show', false)">
                        {{ $t('save') }}
                    </ButtonSubmit>
                </DialogFooter>
            </DialogScrollContent>
        </Dialog>
    </form>

</template>
