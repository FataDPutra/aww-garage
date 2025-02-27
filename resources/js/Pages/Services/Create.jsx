import { useForm } from "@inertiajs/react";

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        service_name: "",
        description: "",
        base_price: "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("services.store"));
    };

    return (
        <div className="p-6">
            <h2 className="text-xl font-bold mb-4">Tambah Layanan</h2>
            <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                    <label className="block font-semibold">Nama Layanan</label>
                    <input
                        type="text"
                        className="border w-full p-2"
                        value={data.service_name}
                        onChange={(e) =>
                            setData("service_name", e.target.value)
                        }
                    />
                    {errors.service_name && (
                        <p className="text-red-500">{errors.service_name}</p>
                    )}
                </div>
                <div>
                    <label className="block font-semibold">Deskripsi</label>
                    <textarea
                        className="border w-full p-2"
                        value={data.description}
                        onChange={(e) => setData("description", e.target.value)}
                    />
                    {errors.description && (
                        <p className="text-red-500">{errors.description}</p>
                    )}
                </div>
                <div>
                    <label className="block font-semibold">Harga Dasar</label>
                    <input
                        type="number"
                        className="border w-full p-2"
                        value={data.base_price}
                        onChange={(e) => setData("base_price", e.target.value)}
                    />
                    {errors.base_price && (
                        <p className="text-red-500">{errors.base_price}</p>
                    )}
                </div>
                <button
                    type="submit"
                    className="bg-blue-500 text-white px-4 py-2 rounded"
                    disabled={processing}
                >
                    Simpan
                </button>
            </form>
        </div>
    );
}
