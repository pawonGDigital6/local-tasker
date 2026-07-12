import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

function fancyBoxInit() {
  // Binds Fancybox to all elements containing the data-fancybox attribute
  Fancybox.bind("[data-fancybox]", {
  });
}

export default fancyBoxInit;
