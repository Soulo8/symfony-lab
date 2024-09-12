import React, { FC, useState } from "react";
import ReactSortableJs from "react-sortablejs";

const ReactSortable = ReactSortableJs.ReactSortable

interface ItemType {
    index: number;
    name: string;
    url: string;
}

const BasicFunction: FC = ({ images }) => {
    const [state, setState] = useState<ItemType[]>(images);

    return (
        <ReactSortable list={state} setList={setState}>
            {state.map((item) => (
                <Image key={item.index} index={item.index} name={item.name} url={item.url}></Image>
            ))}
        </ReactSortable>
    );
};

function Image({ index, name, url }) {
    return <div>
        <input type="hidden" id={`product_images_${index}_id`} name={`product[images][${index}][id]`} />
        <img src={url} alt={name} width="150" />
    </div>
}

export default BasicFunction;
