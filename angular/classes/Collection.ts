import {Subject} from "rxjs";
import {CollectionItem} from "./CollectionItem";

export class Collection<A extends CollectionItem> {
    public elements: { [id: string]: A } = {};

    public changed$: Subject<boolean> = new Subject<boolean>();

    public elementUpdated$: Subject<A> = new Subject<A>();
    public elementDeleted$: Subject<string> = new Subject<string>();

    constructor() {
        this.elementUpdated$.subscribe(this.setElement);
        this.elementDeleted$.subscribe(this.deleteElement)
    }

    public setElement(el: A) {
        this.elements[el.id] = el;
        this.changed$.next(true);
    }

    public deleteElement(elId: string) {
        delete this.elements[elId];
        this.changed$.next(true);
    }
}
