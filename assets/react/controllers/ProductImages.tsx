import React, { FC, useState } from "react";
import ReactSortableJs from "react-sortablejs";

const ReactSortable = ReactSortableJs.ReactSortable

interface ItemType {
    index: number;
    name: string;
    url: string;
}

interface ImageProps {
    key: number;
    index: number;
    name: string;
    url: string;
    onRemove: (index: number) => void;
}

const ProductImages: FC = (props) => {
    const [state, setState] = useState<ItemType[]>(props.images);

    const handleRemove = (index) => {
        setState(prevState => prevState.filter((item) => item.index !== index));
    };

    return (
        <ReactSortable list={state} setList={setState} className="flex flex-wrap gap-4">
            {state.map((item) => (
                <Image key={item.index} index={item.index} name={item.name} url={item.url} onRemove={handleRemove}></Image>
            ))}
        </ReactSortable>
    );
};

function Image({ index, name, url, onRemove }: ImageProps) {
    return (
        <div>
            <input type="hidden" id={`product_images_${index}_id`} name={`product[images][${index}][id]`} />
            <img src={url} alt={name} width="150" />
            <div className="flex justify-center items-center">
                <svg onClick={() => onRemove(index)} className="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                </svg>
            </div>
        </div>
    );
}

export default ProductImages;