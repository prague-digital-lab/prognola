interface _Task {
    id: number,
    uuid: string,
    name: string,
    description?: string,
    user_id: number,
    area_id?: number,
    project_id?: number,
    status: "done" | "todo"|"in_progress",
    deadline_at?: Date,
    worked_at?: Date,
    done_at?: Date,
    created_at?: Date,
    updated_at?: Date,
    specified_at?: Date,
    estimated_seconds?: number,
    worked_seconds?: number
}