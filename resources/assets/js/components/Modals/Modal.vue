<style lang="less" scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  max-width: 50%;
  min-width: 420px;
  margin: 0px auto;
  padding: 15px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header {
  h3 {
    margin: 0 important;
    margin-top: 0;
    color: #2C89D0;
  }
}

.modal-body {
  margin: 0;
  overflow-y: auto;
  max-height: 500px;
}

.modal-default-button {
  float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>

<template>
	<transition name="modal">
		<div class="modal-mask">
			<div class="modal-wrapper">
				<div class="modal-container">

					<div class="modal-header" v-show="hasHeader">
            <h3 class="ma0">
  						<slot name="header">
  							default header
  						</slot>
            </h3>
  				</div>

  				<div class="modal-body" :style="{'max-height': bodyHeight}">
  					<slot name="body">
  						default body
  					</slot>
  				</div>

  				<div class="modal-footer">
  					<slot name="buttons">
  						<button class="modal-default-button" @click="$emit('close')">
  							OK
  						</button>
  					</slot>
  				</div>
  			</div>
      </div>
		</div>
	</transition>
</template>

<script>
export default {
  data() {
    return {
      windowWidth: null,
      windowHeight: null,
    }
  },

  computed: {
    hasHeader() {
      return !!this.$slots['header'];
    },

    bodyHeight() {
      if ( ! this.windowHeight) return '500px';

      return (this.windowHeight * .5) + 'px';
    }
  },

  methods: {
    getWindowDimensions(event) {
      this.windowWidth = document.documentElement.clientWidth;
      this.windowHeight = document.documentElement.clientHeight;
    },
  },

  mounted() {
    this.$nextTick(() => {
      window.addEventListener('resize', this.getWindowDimensions);
      this.getWindowDimensions();
    });
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.getWindowDimensions);
  }
}
</script>