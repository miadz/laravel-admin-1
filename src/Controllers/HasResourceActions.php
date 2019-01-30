<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Form\Builder;

trait HasResourceActions
{
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->form(Builder::MODE_EDIT, $id)->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->form(Builder::MODE_CREATE, null)->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->form(null, $id)->destroy($id)) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}
