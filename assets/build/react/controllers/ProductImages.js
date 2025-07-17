import React, { useState } from "react";
import ReactSortableJs from "react-sortablejs";
const ReactSortable = ReactSortableJs.ReactSortable;
const ProductImages = props => {
  const [state, setState] = useState(props.images);
  const handleRemove = index => {
    setState(prevState => prevState.filter(item => item.index !== index));
  };
  return /*#__PURE__*/React.createElement(ReactSortable, {
    list: state,
    setList: setState,
    className: "flex flex-wrap gap-4"
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
  }), /*#__PURE__*/React.createElement("div", {
    className: "flex justify-center items-center"
  }, /*#__PURE__*/React.createElement("svg", {
    onClick: () => onRemove(index),
    className: "w-6 h-6 text-gray-800 dark:text-white",
    "aria-hidden": "true",
    xmlns: "http://www.w3.org/2000/svg",
    width: "24",
    height: "24",
    fill: "none",
    viewBox: "0 0 24 24"
  }, /*#__PURE__*/React.createElement("path", {
    stroke: "currentColor",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2",
    d: "M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
  }))));
}
export default ProductImages;