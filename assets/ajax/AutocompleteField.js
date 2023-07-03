Vue.component("AutocompleteField", {
  template: `
  <div class="dropdown show" ref="dropdownRef" style="width: 100%">
  <div class="input-group">
    <input
      type="text"
      class="form-control"
      v-model="searchTerm"
      @input="handleInput"
      @keydown.down="highlightNext"
      @keydown.up="highlightPrevious"
      @keydown.enter="handleEnterKey"
    />
    <div class="input-group-append">
      <div class="input-group-text" @click="toggleOptions">
        <i class="fas fa-chevron-down"></i>
      </div>
    </div>
  </div>

  <div class="dropdown-menu dropdown-menu-sm show" v-show="showOptions">
    <div
      class="dropdown-item"
      v-for="(option, index) in filteredOptions"
      :key="index"
      :class="{ activex: option.code === highlightedCode }"
      @mouseover="highlightOption(option.code)"
      @click.prevent="selectOption(option.code)"
    >
      {{ option?.description }}
    </div>
  </div>
</div>

  `,
  props: {
    value: {
      type: [String, Number],
      required: false,
    },
    options: {
      type: [Array],
      required: false,
    },
  },
  data() {
    return {
      searchTerm: "",
      showOptions: false,
      highlightedCode: null,
    };
  },
  computed: {
    filteredOptions() {
      return this.options.filter((option) =>
        option?.description
          ?.toLowerCase()
          .includes(this.searchTerm?.toLowerCase())
      );
    },
  },
  //   watch: {
  //     value(newValue) {
  //       this.highlightedCode = newValue;
  //       const selectedOption = this.options.find(
  //         (option) => option.code === newValue
  //       );
  //       if (selectedOption) {
  //         this.searchTerm = selectedOption.description;
  //       }
  //     },
  //   },
  created() {
    const selectedOption = this.options.find(
      (option) => option.code === this.value
    );
    if (selectedOption) {
      this.searchTerm = selectedOption.description;
      this.highlightedCode = selectedOption.code;
    }
  },
  mounted() {
    document.addEventListener("click", this.handleDocumentClick);
  },
  beforeDestroy() {
    document.removeEventListener("click", this.handleDocumentClick);
  },
  methods: {
    handleInput() {
      this.showOptions = true;
      this.highlightedCode = null;
    },
    highlightOption(code) {
      this.highlightedCode = code;
    },
    highlightNext() {
      if (this.highlightedCode === null) {
        this.highlightedCode = this.filteredOptions[0]?.code;
      } else {
        const currentIndex = this.filteredOptions.findIndex(
          (option) => option.code === this.highlightedCode
        );
        if (currentIndex < this.filteredOptions.length - 1) {
          this.highlightedCode = this.filteredOptions[currentIndex + 1]?.code;
        }
      }
    },
    highlightPrevious() {
      const currentIndex = this.filteredOptions.findIndex(
        (option) => option.code === this.highlightedCode
      );
      if (currentIndex > 0) {
        this.highlightedCode = this.filteredOptions[currentIndex - 1]?.code;
      }
    },
    handleEnterKey() {
      const selectedOption = this.filteredOptions.find(
        (option) => option.code === this.highlightedCode
      );
      if (selectedOption) {
        this.selectOption(selectedOption.code);
      }
    },
    selectOption(code) {
      const selectedOption = this.options.find(
        (option) => option.code === code
      );
      if (selectedOption) {
        this.searchTerm = selectedOption.description;
        this.showOptions = false;
        this.$emit("input", selectedOption.code);
      }
    },
    handleDocumentClick(event) {
      const dropdownElement = this.$refs.dropdownRef;
      const isClickedInside = dropdownElement.contains(event.target);
      if (!isClickedInside) {
        this.showOptions = false;

        if (this.value) {
          this.selectOption(this.value);
        } else {
          this.highlightedCode = null;
        }
      }
    },
    toggleOptions() {
      this.showOptions = !this.showOptions;
    },
  },
});
