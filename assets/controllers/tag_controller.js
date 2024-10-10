import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['subTagList'];

    static values = {
        subTags: Object
    }

    updateSubTags(event) {
        const selectedTag = event.target.value;

        this.subTagListTarget.innerHTML = '<option value=""></option>';

        if (selectedTag in this.subTagsValue) {
            var childrens = this.subTagsValue[selectedTag];

            childrens.forEach(subTag => {
                const option = document.createElement('option');
                option.value = subTag.id;
                option.textContent = subTag.name;
                this.subTagListTarget.appendChild(option);
            });
        }
    }
}