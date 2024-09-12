import React, { useState } from "react";
import ReactSortableJs from "react-sortablejs";
const ReactSortable = ReactSortableJs.ReactSortable;
const BasicFunction = ({
  images
}) => {
  const [state, setState] = useState(images);
  return /*#__PURE__*/React.createElement(ReactSortable, {
    list: state,
    setList: setState
  }, state.map(item => /*#__PURE__*/React.createElement(Image, {
    key: item.index,
    index: item.index,
    name: item.name,
    url: item.url
  })));
};
function Image({
  index,
  name,
  url
}) {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("input", {
    type: "hidden",
    id: `product_images_${index}_id`,
    name: `product[images][${index}][id]`
  }), /*#__PURE__*/React.createElement("img", {
    src: url,
    alt: name,
    width: "150"
  }));
}
export default BasicFunction;