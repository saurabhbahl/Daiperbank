<template>
  <div>
    <div class="flex flex-column justify-between h-100">
      <div class="pa oy-auto">
        <p class="b f2">
          <i
            class="fa"
            :class="childClasses"
          ></i>
          {{ Child.name }}
        </p>

        <div class="flex justify-between">
          <div class="flex-auto w-50 mr3">
            <p class="mb2">
              <span class="b">Zip:</span>
              {{ Child.zip }}
            </p>

            <p class="mb2">
              <span class="b">DOB:</span>
              <span>{{ Child.dob | formatDate("MM/DD/YYYY") }}</span>
            </p>

            <p class="mb2">
              <span class="b">Gender:</span>
              <span>
                <i
                  class="fa"
                  :class="childClasses"
                ></i>
                <span v-if="Child.gender === 'f'">Female</span>
                <span v-else-if="Child.gender === 'm'">Male</span>
              </span>
            </p>

            <p class="mb2">
              <span class="b">Family ID:</span>
              <span>{{ Child.child.guardian.name }}</span>
            </p>
          </div>
          <div class="flex-auto w-50">
            <p class="mb2">
              <span class="b">Current Weight:</span>
              <span>{{ Child.weight_str }}</span>
            </p>

            <p class="mb2">
              <span class="b">Current Age:</span>
              <span>{{ Child.age_str }}</span>
            </p>

            <p class="mb2">
              <span class="b">Potty Training:</span>
              <span>
                <span v-if="Child.status_potty_train">
                  <i class="fa fa-check dark-green"></i>
                  Yes
                </span>
                <span v-else>
                  <i class="fa fa-times dark-red"></i>
                  No
                </span>
              </span>
            </p>

            <p class="mb2">
              <span class="b">Receiving WIC:</span>
              <span>
                <span v-if="Child.status_wic">
                  <i class="fa fa-check dark-green"></i>
                  Yes
                </span>
                <span v-else>
                  <i class="fa fa-times dark-red"></i>
                  No
                </span>
              </span>
            </p>
          </div>
        </div>

        <p class="b f2 bb bw2 b--black-20 mv3">Current Order</p>
        <p class="b f2 bb bw2 b--black-20 mv3" v-if="Child.child.order_count >= 6">You can place only 6 orders for pull ups.</p>
        <table class="table table-condensed table-striped" v-if="!isEditing">
          <thead>
            <tr>
              <th class="w-33">Type</th>
              <th class="w-33">Size</th>
              <th class="w-33">Quantity</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-if="Child.item">{{ Child.item.product.category.name }}</td>
              <td v-else>--</td>

              <td v-if="Child.item">{{ Child.item.product.name }}</td>
              <td v-else>--</td>

              <td v-if="Child.item">{{ Child.item.quantity }}</td>
              <td v-else>--</td>
            </tr>
          </tbody>
        </table>

        <div v-else-if="isEditing">
          <div
            v-if="hasError() && showErrors"
            class="dark-red bg-washed-red pa4 mt3 errorAlertBox"
          >
            <ChildOrderModal :initialErrors="initialErrors" />
            <p v-for="(msg, field) in error()" :key="'error-' + field">
              {{ msg }}
            </p>
          </div>

          <ProductSelector
            :product-categories="productCategories"
            :initial-selected-product="selectedProductId"
            :initial-quantity="selectedProductQuantity"
            :initial-child="Child"
            :all-children="AllChildren"
            @change="onProductChange"
          ></ProductSelector>

          <div class="flex justify-end pt3">
            <div
              class="w-60 mr fg-no"
              v-if="
                selectedProduct.category && selectedProduct.category.id == 2
              "
            >
              <label for="weight"
                >Weight:
                <span class="required">*</span>
              </label>
              <div class="input-group">
                <input
                  type="tel"
                  id="weight"
                  name="weight"
                  class="form-control"
                  v-model="EditedChild.weight"
                  @change="onChildChanged"
                  @keyup="onChildChanged"
                />
                <span class="input-group-addon">lbs</span>
              </div>
            </div>

            <div class="w-40">
              <label for="status_potty_train_yes">Potty Training?</label>
              <p>
                <label for="status_potty_train_yes" class="ma0">
                  <input
                    type="checkbox"
                    class="fwn"
                    id="status_potty_train_yes"
                    v-model="EditedChild.status_potty_train"
                    value="1"
                    @change="onChildChanged"
                    @mouseup="onChildChanged"
                  />
                  Yes
                </label>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Putting this here because IE11 does not remove position:absolute items from the flexbox layout flow
				documentation:
					- https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/12500543/
					- https://stackoverflow.com/questions/32991051/absolutely-positioned-flex-item-is-not-removed-from-the-normal-flow-in-ie11
			-->
      <a href="#" class="pxa pinr pint a-plain pa" @click="close">
        <i class="fa fa-times"></i>
      </a>

      <div class="bg-washed-blue pa bt b--black-20">
        <slot name="buttons"></slot>
      </div>
    </div>
  </div>
</template>

<script>
import ProductSelector from "../General/ProductSelector.vue";
import validation from "../../mixins/validation";
import ChildOrderModal from "../Modals/ChildOrderModal.vue";

export default {
  components: { ProductSelector, ChildOrderModal },
  mixins: [validation],

  props: {
    initialChild: {
      type: Object,
      required: true,
    },

    productCategories: {
      type: Array,
      required: true,
    },

    isEditable: {
      type: Boolean,
      required: false,
      default: true,
    },

    isEditing: {
      type: Boolean,
      required: false,
      default: false,
    },
    allChildren: {
			required: true,
			type: Array,
		},
  },

  data() {
    return {
      AllChildren: this.allChildren,
      Child: this.initialChild,
      EditedChild: this.clone(this.initialChild),
      product_selection_valid: true,
      selectedProduct: {
        ...(this.initialChild.item ? this.initialChild.item.product : []),
        quantity: this.initialChild.item
          ? this.initialChild.item.quantity
          : null,
      },
      editing_reason: null,
      flag_editing_reason_share: false,
      allow_child_note: false,
    };
  },

  computed: {
    showErrors() {
      return (
        this.selectedProduct.product_id != null ||
        this.selectedProduct.quantity != null ||
        this.EditedChild.weight != null
      );
    },

    selectedProductId() {
      if (this.Child.item) {
        return this.Child.item.product_id;
      }

      return null;
    },

    selectedProductQuantity() {
      if (this.Child.item) {
        return this.Child.item.quantity || null;
      }

      return null;
    },

		childClasses() {
			if(this.Child.is_menstruator == 1){
				return 'fa-female purple-female'
			}
			else{
				return {
					'fa-female': this.Child.gender === 'f',
					'fa-male': this.Child.gender === 'm',
				};
			}
		},
  },

  methods: {
    close() {
      this.$emit("close");
    },

    onChildChanged() {
      this.$emit("change", this.EditedChild);
    },

    onProductChange(valid, selectedProduct) {
      // update order count in database
      let child_id = this.Child.child_id; 
      axios.post(`/api/child/updateorder/count`,{selectedProduct,child_id})
      .then( response => {
        console.log(response);
      })
      .catch( error => {
        
      });

      this.product_selection_valid = valid;
      this.selectedProduct = selectedProduct;
      let existingProduct = {
        product_id: this.EditedChild.product_id,
        quantity: this.EditedChild.quantity,
      };

      this.EditedChild.product_id = selectedProduct.id;
      this.EditedChild.quantity = selectedProduct.quantity;
      this.EditedChild.order_count = selectedProduct.order_count;

      if (
        existingProduct.product_id != this.EditedChild.product_id ||
        existingProduct.quantity != this.EditedChild.quantity
      ) {
        this.onChildChanged();
      }
    },

    clone(object) {
      return JSON.parse(JSON.stringify(object));
    },
  },
};
</script>
