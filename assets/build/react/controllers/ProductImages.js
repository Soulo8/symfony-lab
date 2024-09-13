import React, { useState } from "react";
import ReactSortableJs from "react-sortablejs";
const ReactSortable = ReactSortableJs.ReactSortable;
const BasicFunction = props => {
  const [state, setState] = useState(props.images);
  const handleRemove = index => {
    setState(prevState => prevState.filter(item => item.index !== index));
  };
  return /*#__PURE__*/React.createElement(ReactSortable, {
    list: state,
    setList: setState
  }, state.map(item => /*#__PURE__*/React.createElement(Image, {
    key: item.index,
    index: item.index,
    name: item.name,
    url: item.url,
    onRemove: handleRemove
  })));
};
function Image({
  index,
  name,
  url,
  onRemove
}) {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("input", {
    type: "hidden",
    id: `product_images_${index}_id`,
    name: `product[images][${index}][id]`
  }), /*#__PURE__*/React.createElement("img", {
    src: url,
    alt: name,
    width: "150"
  }), /*#__PURE__*/React.createElement("span", {
    onClick: () => onRemove(index)
  }, "Supprimer"));
}
export default BasicFunction;