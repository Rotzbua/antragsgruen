import {Component, ElementRef} from '@angular/core';
import {WebsocketService} from "./websocket.service";
import {Collection} from "../classes/Collection";
import {Motion} from "../classes/Motion";
import {debounceTime} from 'rxjs/operators';
import {Amendment} from "../classes/Amendment";

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    public log: string = '';
    public motionCollection: Collection<Motion> = new Collection<Motion>();
    public amendmentCollection: Collection<Amendment> = new Collection<Amendment>();
    public sortedMotions: Motion[];
    public sortedAmendments: Amendment[];

    public constructor(private _websocket: WebsocketService, el: ElementRef) {
        this._websocket.debuglog$.subscribe((str) => {
            this.log += str + "\n";
        });
        this._websocket.authenticated$.subscribe((user) => {
            this._websocket.subscribeChannel(1, "motions");
            this._websocket.subscribeChannel(1, "amendments");
        });

        this._websocket.motions$.subscribe((motion) => {
            this.motionCollection.setElement(motion);
        });
        // Debounce: if a collection comes, don't recalculate the UI for each element
        this._websocket.motions$.asObservable().pipe(debounceTime(1)).subscribe(this.recalcMotionList.bind(this));

        this._websocket.amendments$.subscribe((amendment) => {
            this.amendmentCollection.setElement(amendment);
        });
        // Debounce: if a collection comes, don't recalculate the UI for each element
        this._websocket.amendments$.asObservable().pipe(debounceTime(1)).subscribe(this.recalcMotionList.bind(this));

        this._websocket.connect(el.nativeElement.getAttribute("cookie"));
    }

    private recalcMotionList() {
        this.sortedMotions = Object.keys(this.motionCollection.elements).map(key => this.motionCollection.elements[key]);
        this.sortedAmendments = Object.keys(this.amendmentCollection.elements).map(key => this.amendmentCollection.elements[key]);
        console.log(this.motionCollection);
        console.log(this.amendmentCollection);
        console.log(this.sortedMotions);
        console.log(this.sortedAmendments);
    }
}
