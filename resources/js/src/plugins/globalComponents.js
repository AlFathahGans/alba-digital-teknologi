import Badge from "../assets/components-argon/Badge.vue";
import BaseAlert from "../assets/components-argon/BaseAlert.vue";
import BaseButton from "../assets/components-argon/BaseButton.vue";
import BaseCheckbox from "../assets/components-argon/BaseCheckbox.vue";
import BaseInput from "../assets/components-argon/BaseInput.vue";
import BasePagination from "../assets/components-argon/BasePagination.vue";
import BaseProgress from "../assets/components-argon/BaseProgress.vue";
import BaseRadio from "../assets/components-argon/BaseRadio.vue";
import BaseSlider from "../assets/components-argon/BaseSlider.vue";
import BaseSwitch from "../assets/components-argon/BaseSwitch.vue";
import Card from "../assets/components-argon/Card.vue";
import Icon from "../assets/components-argon/Icon.vue";

export default {
  install(Vue) {
    Vue.component(Badge.name, Badge);
    Vue.component(BaseAlert.name, BaseAlert);
    Vue.component(BaseButton.name, BaseButton);
    Vue.component(BaseInput.name, BaseInput);
    Vue.component(BaseCheckbox.name, BaseCheckbox);
    Vue.component(BasePagination.name, BasePagination);
    Vue.component(BaseProgress.name, BaseProgress);
    Vue.component(BaseRadio.name, BaseRadio);
    Vue.component(BaseSlider.name, BaseSlider);
    Vue.component(BaseSwitch.name, BaseSwitch);
    Vue.component(Card.name, Card);
    Vue.component(Icon.name, Icon);
  }
};
