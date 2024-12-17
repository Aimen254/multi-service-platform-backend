<template>
    <NotificationGroup group="toast">
        <Notification v-slot="{ notifications }" enter="transform ease-out duration-3000 transition"
            enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            enter-to="translate-y-0 opacity-100 sm:translate-x-0" leave="transition ease-in duration-5000"
            leave-from="opacity-100" leave-to="opacity-0" move="transition duration-500" move-delay="delay-3000">
            <div class="" v-for="notification in notifications" :key="notification.id">
                <div class="" v-if="notification.type === 'success'">
                    <div id="toastr-container" class="alert toastr-bottom-right">
                        <div class="toastr toastr-success" aria-live="polite" style="">
                            <button type="button" class="toastr-close-button" data-bs-dismiss="alert" aria-label="Close"
                                @click.prevent="hideAlert">×</button>
                            <div class="toastr-message">{{notification.text}}</div>
                        </div>
                    </div>
                </div>
                <div v-if="notification.type === 'error'">
                    <div id="toastr-container" class="alert toastr-bottom-right">
                        <div class="toastr toastr-error" aria-live="assertive" style="">
                            <button type="button" class="toastr-close-button" data-bs-dismiss="alert" aria-label="Close"
                                @click.prevent="hideAlert">×</button>
                            <div class="toastr-message">{{notification.text}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </Notification>
    </NotificationGroup>
</template>
