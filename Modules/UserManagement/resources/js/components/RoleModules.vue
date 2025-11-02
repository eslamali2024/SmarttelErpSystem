<script setup lang="ts">
import { ref, computed } from 'vue'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue'
import RoleModules from './RoleModules.vue'

const props = defineProps<{
    module?: Record<string, any>
    level?: number
    permKey?: string
    modelValue?: string[]
    isReadOnly?: boolean
}>()

const emit = defineEmits(['update:modelValue'])
const open = ref(true)
const hasChildren = computed(() => !!props.module?.children)
const padding = computed(() => `${(props.level || 0) * 1.5}rem`)

// Get all permissions
const getAllPermissions = (mod: Record<string, any>): string[] => {
    let perms: string[] = []
    if (mod.children) {
        for (const child of Object.values(mod.children)) {
            perms = perms.concat(getAllPermissions(child))
        }
    }

    // Access
    if (mod.access) perms.push(mod.access)
    for (const val of Object.values(mod)) {
        if (typeof val === 'string' && val.includes('_')) perms.push(val)
    }
    return perms
}


// Handle checkbox click
const checkboxHandle = (value: string) => {
    const current = props.modelValue || []
    const newValue = current.includes(value)
        ? current.filter(i => i !== value)
        : [...current, value]
    emit('update:modelValue', newValue)
}

// Handle Select All
const toggleSelectAll = () => {
    if (props.isReadOnly) return
    const allPerms = getAllPermissions(props.module!)
    const current = props.modelValue || []
    const allSelected = allPerms.every(p => current.includes(p))

    let newValue: string[]
    if (allSelected) {
        // Unselect all
        newValue = current.filter(i => !allPerms.includes(i))
    } else {
        // Select all
        const toAdd = allPerms.filter(i => !current.includes(i))
        newValue = [...current, ...toAdd]
    }
    emit('update:modelValue', newValue)
}

// Check if all permissions are selected
const isAllSelected = computed(() => {
    if (!props.module) return false
    const allPerms = getAllPermissions(props.module)
    return allPerms.every(p => props.modelValue?.includes(p))
})
</script>
<template>
    <div class="space-y-2" :style="{ paddingLeft: padding }">
        <!-- Parent Module -->
        <div v-if="module"
            class="flex justify-between items-center bg-gray-100 dark:bg-gray-700/50  p-2 rounded cursor-pointer hover:bg-gray-200 hover:dark:bg-gray-700 duration-200"
            @click.self="open = !open">
            <div class="flex items-center gap-2">
                <span v-if="hasChildren" class="duration-200">
                    <i v-if="!open" class="ri ri-arrow-right-s-line"></i>
                    <i v-if="open" class="ri ri-arrow-down-s-line"></i>
                </span>
                <h3 class="uppercase font-semibold text-gray-700 dark:text-white">{{ module.title ?? permKey }}</h3>
            </div>

            <div class="flex flex-col gap-2">
                <!-- Select All -->
                <label
                    class="text-sm font-medium cursor-pointer flex gap-2 items-center border rounded p-1 hover:bg-white hover:dark:bg-gray-600 duration-200">
                    <Checkbox :id="'select-all-' + module.access" :value="module.key" :model-value="isAllSelected"
                        @click.stop="toggleSelectAll" :disabled="props.isReadOnly" />
                    <span @click.stop="toggleSelectAll">{{ $t('select_all') }}</span>
                </label>

                <!-- Access Checkbox -->
                <label :for="module.access" v-if="module.access"
                    class="text-sm font-medium cursor-pointer flex gap-2 items-center border rounded p-1 hover:bg-white hover:dark:bg-gray-600 duration-200">
                    <Checkbox :id="module.access" name="permissions[]" :value="module.access"
                        :model-value="props.modelValue?.includes(module.access)"
                        @click.stop="checkboxHandle(module.access)" :disabled="props.isReadOnly" />
                    {{ $t('access') }}
                </label>

            </div>
        </div>

        <!-- Children Modules (Recursive) -->
        <div v-if="hasChildren && open" class="pl-4 border-l border-gray-300 duration-200">
            <div v-for="(child, key) in module.children" :key="key">
                <RoleModules :module="child" :level="(props.level || 0) + 1" :model-value="props.modelValue"
                    :permKey="key" @update:modelValue="emit('update:modelValue', $event)"
                    :isReadOnly="props.isReadOnly" />
            </div>
        </div>

        <!-- Leaf Permissions -->
        <div v-else-if="!hasChildren && typeof module === 'object'" class="pl-4 space-y-1 mb-2">
            <div v-for="(permValue, permKey) in module" :key="permKey">
                <div v-if="typeof permValue === 'string' && permValue.includes('_') && permKey != 'access'"
                    class="flex items-center gap-2">
                    <Checkbox :id="permValue" name="permissions[]" :value="permValue"
                        :model-value="props.modelValue?.includes(String(permValue))"
                        @click.stop="checkboxHandle(String(permValue))" :disabled="props.isReadOnly" />
                    <label :for="permValue" class="capitalize">{{ permKey }}</label>
                </div>
            </div>
        </div>

    </div>
</template>
