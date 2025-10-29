<template>
    <div class="card">
    </div>

    <div class="p-4">
        <div class="bg-gray-50 border dark:bg-gray-500/50 p-4 rounded-xl">
            <div class="flex justify-between items-center">
                <slot name="header" />
            </div>

            <hr class="my-2 w-5/6 mx-auto">

            <slot name="body" />

            <hr class="my-2 w-5/6 mx-auto" v-if="$slots.footer" />

            <slot name="footer" />

        </div>
    </div>
</template>
