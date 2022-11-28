<template>
	<div>
		<div class="form-control flex justify-stretch items-center selected-item clickable"
			style="height: auto;"
			ref="search-container"
			:class='{"open": open}'
			@mousedown.prevent="toggleSearch">

			<div class="o-hidden flex-auto">
				<span v-for="(item, idx) in valueAsArray" :key="idx"
					class="dib mr fl mv1 mr2">
					<slot name="selected-item" v-bind="item">
						{{ getItemLabel(item) }}
					</slot>
				</span>

				<span v-if=" ! valueAsArray.length"
					class="dib mr fl mv1 mr2">
					<slot name="default-selection">
						Select one
					</slot>
				</span>

				<input
					v-if="search"
					ref="search"
					class="bw0 ma0 fl pa0 no"
					:style='{"width": searchQueryWidth + "px"}'
					style="max-width: 100%;"
					type="search"
					v-model="query"
					@focus.stop="onFocus"
					@blur.stop="onBlur"
					@keyup.esc.stop="onEscape"
					@keyup="onSearchKeyUp"
					@keydown.up.prevent="typeAheadUp"
					@keydown.down.prevent="typeAheadDown"
					@keyup.enter.prevent="typeAheadSelect"
					@keydown.enter.prevent=""
				>
			</div>

			<i class="fa fa-chevron-down"
				:class='{"fa-rotate-180": open }'
				ref="openIndicator"
				></i>
		</div>

		<transition>
			<ul v-if="open"
				ref="search-results"
				class="search-results pxf oy-auto ba br3 ma0 b--black-20 shadow-2"
				style="max-height: 300px;"
				:style='{"width": searchPosition.width + "px", "top": (searchPosition.top + searchPosition.height + searchMargin) + "px"}'>
				<li
					v-for="(item, index) in filteredItems"
					:key="index"
					class="ma0 clickable"
					:class="itemClasses(index, item)">
					<a @mousedown.prevent="select(item)" class="db a-plain">
						<slot name="item" v-bind="item">
							{{ getItemLabel(item) }}
						</slot>
					</a>
				</li>

				<li v-if="filteredItems.length == 0"
					class="ma0">
					<a @mousedown.prevent="emptyClicked(query)" class="db a-plain">
						<slot name="empty-results" v-bind="{ query }">
							No results found.
						</slot>
					</a>
				</li>
			</ul>
		</transition>
	</div>
</template>

<script>
import Fuse from 'fuse.js';
import $ from 'jquery';
import typeAHeadPointer from './mixins/typeAheadPointer.js';
import pointerScroll from './mixins/pointerScroll.js';

export default {
	props: {
		items: {
			required: true,
		},

		initialValue: {
			required: false,
			default: null,
		},

		valueKey: {
			required: false,
			type: [String, Function],
			default: 'id',
		},

		labelKey: {
			required: false,
			type: [String, Function],
			default: 'label',
		},

		searchFields: {
			required: false,
			type: [String, Array],
			default: null,
		},

		search: {
			required: false,
			type: [Boolean],
			default: true,
		},
	},

	mixins: [ typeAHeadPointer, pointerScroll ],

	data() {
		return {
			query: '',
			value: this.initialValue,
			open: false,
			windowWidth: null,
			windowHeight: null,
			searchPosition: {},
			searchMargin: 4,
			scrollListeners: [],
			searchQueryWidth: 25,
			clearSearchOnSelect: true,
			closeOnSelect: true,
			fuse: this.makeFuse(this.search),
		};
	},

	computed: {
		searchQuery() {
			return this.query.trim();
		},

		hashedItems() {
			let hashed = {};
			this.items.map( item => {
				hashed[ item[this.valueKey] ] = item;
			});

			return hashed;
		},

		filteredItems() {
			if (this.searchQuery.length == 0) return this.items;

			let item_ids = this.fuse.search(this.searchQuery).map( result => {
				return String(result.item);
			});

			return this.items.filter( item => {
				return item_ids.indexOf(String(item[ this.valueKey ])) >= 0;
			});
		},

		showSearchResults() {
			return this.searchQuery.length;
		},

		valueAsArray() {
			const selectedValues = (this.value instanceof Array? this.value : [ this.value ]);

			return this.items.filter( (item) => {
				return selectedValues.indexOf(item[ this.valueKey ]) >= 0;
			});
			// return this.value instanceof Array? this.value : [ this.value ];
		},
	},

	methods: {
		select(item) {
			if (this.multiple && !this.value) {
				this.value = [item[ this.valueKey ]]
			} else if (this.multiple) {
				this.value.push(item[ this.valueKey ])
			} else {
				this.value = item[ this.valueKey ]
			}

			this.$emit('selected', this.value);

			if (this.closeOnSelect) {
				this.toggleSearch();
			}
		},

		emptyClicked(query) {
			this.$emit('emptyClicked', query);
		},

		choose(item) {
			this.$emit('chosen', item);
			this.clearQuery();
		},

		create() {
			this.$emit('new', this.searchQuery);
			this.clearQuery();
		},

		clearQuery() {
			this.query = '';
		},

		getItemLabel(item) {
			return item[name];
		},

		itemClasses(idx, item) {
			return {
				'selected': idx == this.typeAheadPointer || this.value == item[ this.valueKey ],
			};
		},

		toggleSearch() {
			if (this.open) {
				if (this.search && document.activeElement == this.$refs.search) {
					this.$refs.search.blur();
				}
				else {
					this.onBlur();
				}
			}
			else {
				if (this.search) {
					this.$refs.search.focus();
				}
				else {
					this.onFocus();
				}
			}
		},

		onFocus() {
			this.open = true;
			this.typeAheadPointer = -1;
			this.$nextTick(() => {
				this.getWindowDimensions()
			});
			document.addEventListener('keydown', this.preventBrowserDefaultEscape);
		},

		onBlur() {
			this.open = false;
			this.query = '';
			document.removeEventListener('keydown', this.preventBrowserDefaultEscape);
		},

		onSearchKeyUp(evt) {
			this.updateSearchQueryWidth();
			this.getSearchPosition();
		},

		onEscape() {
			if (0 == this.query.length) {
				this.$refs.search.blur()
			} else {
				this.query = ''
			}

			return false;
		},

		preventBrowserDefaultEscape(evt) {
			if (evt.keyCode == 27) {
				evt.preventDefault();
				return false;
			}
		},

		updateSearchQueryWidth(evt) {
			let tmpStyles = {
				position: 'absolute',
				left: '-1000px',
				top: '-1000px',
				display: 'none',
				whiteSpace: 'pre',
			};
			const duplicateStyles = ['fontSize', 'fontStyle', 'fontWeight', 'fontFamily', 'lineHeight', 'textTransform', 'letterSpacing']
			const $searchField = $(this.$refs['search']);

			tmpStyles = duplicateStyles.reduce( (styles, property) => {
				styles[ property ] = $searchField.css( property );
				return styles;
			}, tmpStyles);

			const tmpDiv = $('<div />').css(tmpStyles);
			tmpDiv.text( this.query );
			$('body').append(tmpDiv);

			const width = Math.max(tmpDiv.width() + 25, 25);

			tmpDiv.remove();

			this.searchQueryWidth = width;
			return width;
		},

		getWindowDimensions(event) {
			this.windowWidth = document.documentElement.clientWidth;
			this.windowHeight = document.documentElement.clientHeight;
		},

		getSearchPosition() {
			this.searchPosition = this.$refs['search-container'].getBoundingClientRect();
		},

		attachScrollEvent(callback) {
			let scrollListeners = $(this.$refs['search-container']).parents().filter( (idx, el) => {
				const $el = $(el);
				const properties = ['overflow', 'overflow-y', 'overflow-x'];
				const values = ['scroll', 'auto'];

				return properties.filter( property => {
					return values.filter( value => {
						return $el.css(property) == value;
					}).length > 0;
				}).length > 0;
			});

			scrollListeners.push(window);

			this.scrollListeners = scrollListeners;
			scrollListeners.map((idx, el) => {
				el.addEventListener('scroll', callback);
			});
		},

		detatchScrollEvent(callback) {
			this.scrollListeners.map((idx, el) => {
				el.removeEventListener('scroll', callback);
			});
		},

		makeFuse(searchEnabled) {
			if (!searchEnabled) return false;

			return new Fuse(this.items, {
				findAllMatches: true,
				includeMatches: true,
				threshold: .45,
				distance: 25,
				maxPatternLength: 32,
				minCharLength: 1,
				id: this.valueKey,
				keys: [
					...(this.searchFields || [])
				],
			});
		}
	},

	mounted() {
		this.$nextTick( () => {
			window.addEventListener('resize', this.getWindowDimensions);
			window.addEventListener('resize', this.getSearchPosition);
			this.attachScrollEvent(this.getSearchPosition);

			//Init
			this.getWindowDimensions();
			this.getSearchPosition();
		});
	},

	beforeDestroy() {
		window.removeEventListener('resize', this.getWindowDimensions);
		window.removeEventListener('resize', this.getSearchPosition);
		this.detatchScrollEvent(this.getSearchPosition);
		document.removeEventListener('keydown', this.preventBrowserDefaultEscape);
	}
}
</script>

<style lang="less" scoped>
.selected {
	background: #ddd;
}

.search-results {
	background: #FFF;
	list-style: none;
	padding: 0;
	margin: 0;

	li {
		padding: 0;
		border-bottom: 1px solid #eee;

		a { padding: 5px 10px; }
		p { margin: 0; }

		&:hover {
			cursor: pointer;
			.selected;
		}
	}
}
</style>