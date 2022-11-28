
<template>
	<div :class="{'modal-container': true, 'mod--show': this.visible}">
			<component :is="modal" v-bind="props" v-custom-events="events"></component>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
	computed: {
		...mapGetters('Modal', {
			visible: 'visible',
			modal: 'modalComponent',
			props: 'props',
			events: 'events',
		}),
	},

	directives: {
		CustomEvents: {
			bind: function (el, binding, vnode) {
                const allEvents = binding.value;
                if(typeof allEvents !== "undefined"){
                    const allEventNames = Object.keys(binding.value);
                    allEventNames.forEach( event_name => {
                        vnode.componentInstance.$on(event_name, (eventData) => {
                            allEvents[event_name](eventData);
                        });
                    });
                }
            },
            unbind: function (el, binding, vnode) {
                vnode.componentInstance.$off();
            }
		}
	}
}
</script>